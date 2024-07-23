<?php

namespace App\HHH_Library\ThisApp\Packages\Client\TrustScore;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ClientTrustScoresTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Jobs\ClientsManagement\EffectBlockedDomainJob;
use App\Jobs\ClientsManagement\EffectFakeDomainReportJob;
use App\Models\BackOffice\ClientsManagement\ClientTrustScore;
use App\Models\BackOffice\Domains\AssignedDomain;
use App\Models\BackOffice\Domains\Domain;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ClientTrustScoreEngine
{
    private ?User $user;

    private const MAX_TRUST_SCORE = 100;
    private const MIN_DOMAIN_SUSPICIOUS_SCORE = -4; // A client with a higher score is suspicious

    /**
     * __construct
     *
     * @param  \App\Models\User $user
     * @return void
     */
    function __construct(?User $user = null)
    {
        /** @var User $user */
        $user = is_null($user) ? auth()->user() : $user;

        if (!is_null($user)) {
            if ($user->isClient()) {
                $this->user = $user;
                $this->updateTrustScore();
            } else
                $this->user = null;
        } else
            $this->user = null;
    }

    /**
     * Get client trust score
     *
     * @return int
     */
    public function getTrustScore(): int
    {
        if (!is_null($this->user))
            if ($this->user->isClient()) {

                if ($clientTrustScore = $this->user->clientTrustScore) {
                    return $clientTrustScore[TableEnum::Score->dbName()];
                }
            }

        return -1;
    }

    /**
     * Get client domain suspicious score
     *
     * @return int
     */
    public function getDomainSuspiciousScore(): int
    {
        if (!is_null($this->user))
            if ($this->user->isClient()) {

                if ($clientTrustScore = $this->user->clientTrustScore) {
                    return $clientTrustScore[TableEnum::DomainSuspicious->dbName()];
                }
            }

        return 0;
    }

    /**
     * Check if the client is suspicious
     *
     * Note:
     * The new user is included among the suspicious users to prove otherwise.
     *
     * @return bool
     */
    public function isClientSuspicious(): bool
    {
        if (!is_null($this->user)) {

            if ($clientTrustScore = $this->user->clientTrustScore) {
                return $clientTrustScore[TableEnum::DomainSuspicious->dbName()] > self::MIN_DOMAIN_SUSPICIOUS_SCORE ? true : false;
            }
        }

        return true;
    }

    /**
     * Notify the trust score engine when the assigned domain is reported by user,
     * so that it lowers the user's trust score
     *
     * @param null|\App\Models\BackOffice\Domains\AssignedDomain $assignedDomain
     * @return void
     */
    public static function assignedDomainReported(?AssignedDomain $assignedDomain): void
    {
        if (is_null($assignedDomain))
            return;

        $domain = $assignedDomain->domain;

        $fastReportTime = Carbon::now()->subMinutes(5);

        if (!is_null($domain[DomainsTableEnum::AnnouncedAt->dbName()]))
            if ($domain[DomainsTableEnum::AnnouncedAt->dbName()] > $fastReportTime) {
                // The user registered a report in less than 5 minutes from the public announcement of the domain.

                $negativePointValue = AppTechnicalSettingsEnum::TrScSy_NegativePointValue->getValue();

                /** @var ClientTrustScore $clientTrustScore */
                $clientTrustScore = $assignedDomain->user->clientTrustScore;

                if (!is_null($clientTrustScore)) {
                    $clientTrustScore->decrement(TableEnum::Score->dbName(), $negativePointValue * 5);
                    $clientTrustScore->increment(TableEnum::DomainSuspicious->dbName(), 1);
                }
            }
    }

    /**
     * Notify the trust score engine when the domain is blocked,
     * so that it lowers the user's trust score
     *
     * @param  null|\App\Models\BackOffice\Domains\Domain $domain
     * @return void
     */
    public static function domainBlocked(?Domain $domain): void
    {
        EffectBlockedDomainJob::dispatch($domain);
    }

    /**
     * Notify the trust score engine when the domain is reported fake,
     * so that it lowers the user's trust score
     *
     * @param  null|\App\Models\BackOffice\Domains\Domain $domain
     * @return void
     */
    public static function domainReportedFake(?Domain $domain): void
    {
        EffectFakeDomainReportJob::dispatch($domain);
    }

    /**
     * Update client trust score
     *
     * @return void
     */
    private function updateTrustScore(): void
    {
        $user = $this->user;

        if (is_null($user))
            return;

        if ($user->isPersonnel())
            return;

        $clientExtra = $user->betconstructClient;

        if (is_null($clientExtra)) {

            // Logout client to fetch data again
            if ($user->id == auth()->user()->id)
                Auth::logout();

            return;
        }

        $tsUserIdCol  = TableEnum::UserId->dbName();
        $tsScoreCol  = TableEnum::Score->dbName();
        $tsDepositCountCol  = TableEnum::DepositCount->dbName();
        $tsBalanceCol  = TableEnum::Balance->dbName();

        $clientDepositCount = $clientExtra[ClientModelEnum::DepositCount->dbName()];
        $clientCurrency = $clientExtra[ClientModelEnum::CurrencyId->dbName()];
        $clientBalance = $clientExtra[ClientModelEnum::Balance->dbName()];
        $clientUnplayedBalance = $clientExtra[ClientModelEnum::UnplayedBalance->dbName()];

        $clientTrustScore = ClientTrustScore::where(TableEnum::UserId->dbName(), $user->id)->first();

        if (is_null($clientTrustScore)) {
            // Set new trust score

            $trustScore = AppTechnicalSettingsEnum::TrScSy_NewClientBaseTrustScore->getValue();

            $trustScore += $this->getDepositCountScore($clientDepositCount);
            $trustScore += $this->getBalanceScore($clientCurrency, $clientBalance + $clientUnplayedBalance);

            $clientTrustScore = new ClientTrustScore();
            $clientTrustScore->$tsUserIdCol = $this->user->id;
        } else {
            // Update trust score

            $trustScore = $clientTrustScore->$tsScoreCol;
            $trustDepositCount = $clientTrustScore->$tsDepositCountCol;
            $trustBalance = $clientTrustScore->$tsBalanceCol;

            $depositCountDifference = $clientDepositCount - $trustDepositCount;
            $trustScore += $depositCountDifference > 0 ? $depositCountDifference : 0;
            $trustScore += $this->getBalanceScore($clientCurrency, $clientBalance - $trustBalance);
        }

        if ($trustScore > self::MAX_TRUST_SCORE)
            $trustScore = self::MAX_TRUST_SCORE;

        if ($trustScore < 1) {
            /**
             * Increasing the trust score of users who have a negative score
             *  for more than 24 hours for reappraisal.
             */

            $expireTime = Carbon::now()->subDay();

            if ($clientTrustScore[TimestampsEnum::UpdatedAt->dbName()] < $expireTime)
                $trustScore += (AppTechnicalSettingsEnum::TrScSy_NegativePointValue->getValue() * 3);
        }

        $clientTrustScore->forceFill([

            $tsScoreCol         => $trustScore,
            $tsDepositCountCol  => $clientDepositCount,
            $tsBalanceCol       => $clientBalance,
        ]);

        $clientTrustScore->save();
    }

    /**
     * Get client deposit count score
     *
     * @param  ?int $depositCount
     * @return int
     */
    private function getDepositCountScore(?int $depositCount): int
    {
        if (is_null($depositCount))
            $depositCount = 0;

        $depositPerPoint = AppTechnicalSettingsEnum::TrScSy_DepositPerPoint->getValue();

        $trustScore = number_format($depositCount / $depositPerPoint, 0, '.', '');

        return (int) $trustScore;
    }

    /**
     * Get client deposit count score
     *
     * @param string $currency
     * @param  ?int $balance
     * @return int
     */
    private function getBalanceScore(string $currency, ?int $balance): int
    {
        if (!is_null($currency))
            $currency = strtolower($currency);

        if (is_null($balance))
            $balance = 0;

        if ($balance <= 0)
            return 0;

        $unit = match ($currency) {

            'usd'   => AppTechnicalSettingsEnum::TrScSy_UsdPerPoint->getValue(),
            'irt'   => AppTechnicalSettingsEnum::TrScSy_IrtPerPoint->getValue(),
            'tom'   => AppTechnicalSettingsEnum::TrScSy_TomPerPoint->getValue(),
            'irr'   => AppTechnicalSettingsEnum::TrScSy_IrrPerPoint->getValue(),

            default => 0
        };

        $trustScore = ($unit > 0) ? ($balance / $unit) : 0;

        return (int) number_format($trustScore, 0, '.', '');
    }
}
