<?php

namespace App\Jobs\Referral;

use App\Constants\DelayConstants;
use App\Enums\Database\Tables\BetsTableEnum;
use App\Enums\Database\Tables\ClientSyncsTableEnum;
use App\Enums\Database\Tables\ReferralBetsConclusionsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum;
use App\Enums\General\QueueEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Enums\Database\Tables\ReferralSessionsTableEnum;
use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\General\CurrencyEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Jobs\FetchData\Single\FetchClientBetsJob;
use App\Jobs\FetchData\Single\FetchClientExtraDataJob;
use App\Models\BackOffice\Referral\Referral;
use App\Models\BackOffice\Referral\ReferralBetsConclusion;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Models\BackOffice\Referral\ReferralSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReferralBetsConclusionJob implements ShouldQueue
{
    /**
     * This job conclusion the bets of referred client base on referrer currency
     */

    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // __construct variables
    private $referredClientId; // user id of referred client

    // Private variables
    private Carbon $beginDate, $endDate, $calculatedUntil;
    private ReferralSession $referralSession;
    private ReferralRewardPackage $referralRewardPackage;
    private User $referrerUser, $referredUser;
    private BetconstructClient $referrerUserExtra, $referredUserExtra;
    private CurrencyEnum $referrerCurrency, $referredCurrency;

    /**
     * Create a new job instance.
     *
     * @param int $referredClientId
     */
    public function __construct(int $referredClientId)
    {
        $this->onQueue(QueueEnum::ReferralBetsConclusion->value);

        $this->referredClientId = $referredClientId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        if (!AppSettingsEnum::IsCommunityActive->getValue()) {

            self::dispatch($this->referredClientId)->delay(now()->addMinutes(DelayConstants::CommunityIsNotActive));
            return;
        }

        $initRes = $this->init();
        if (is_string($initRes)) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                "Error: " . $initRes,
                "Issue in init function"
            );
            return;
        } else if ($initRes === false)
            return;

        try {

            $user = $this->referredUser;
            $minBetOdds = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetOddsReferred->dbName()];
            $minBetAmount = $this->getMinBetAmount();

            $bets = $user->clientBets()
                ->where(BetsTableEnum::IsReferralBet->dbName(), 1)
                ->where(BetsTableEnum::Odds->dbName(), '>=', $minBetOdds)
                ->where(BetsTableEnum::Amount->dbName(), '>=', $minBetAmount)
                ->where(BetsTableEnum::CalculatedAt->dbName(), '>=', $this->beginDate->toDateTimeString())
                ->where(BetsTableEnum::CalculatedAt->dbName(), '<=', $this->calculatedUntil->toDateTimeString());

            $betsCount = $bets->count();
            $betsTotalAmount = $bets->sum(BetsTableEnum::Amount->dbName());
            $calculateTime = now()->toDateTimeString();
            $betsTotalAmountExchanged = $this->referredCurrency->exchange($betsTotalAmount, $this->referrerCurrency);

            $referralBetsConclusion = ReferralBetsConclusion::where(ReferralBetsConclusionsTableEnum::ReferralSessionId->dbName(), $this->referralSession[ReferralSessionsTableEnum::Id->dbName()])
                ->where(ReferralBetsConclusionsTableEnum::ReferredId->dbName(), $this->referredClientId)
                ->first();

            if (is_null($referralBetsConclusion))
                $referralBetsConclusion = new ReferralBetsConclusion();

            $referralBetsConclusion->forceFill([

                ReferralBetsConclusionsTableEnum::ReferralSessionId->dbName()   => $this->referralSession->id,
                ReferralBetsConclusionsTableEnum::ReferrerId->dbName()          => $this->referrerUser->id,
                ReferralBetsConclusionsTableEnum::ReferredId->dbName()          => $this->referredUser->id,
                ReferralBetsConclusionsTableEnum::BetsCount->dbName()           => $betsCount,
                ReferralBetsConclusionsTableEnum::BetsTotalAmount->dbName()     => $betsTotalAmountExchanged,
                ReferralBetsConclusionsTableEnum::CalculatedUntil->dbName()     => $this->calculatedUntil->toDateTimeString(),
                ReferralBetsConclusionsTableEnum::CalculatedAt->dbName()        => $calculateTime,
            ]);

            $referralBetsConclusion->save();

            /**************** Unlock Queue bet conclusion lock ****************/
            if ($referredReferral = $referralBetsConclusion->referredReferral) {
                $referredReferral[ReferralsTableEnum::BetsConclusionQueuedAt->dbName()] = null;
                $referredReferral->save();
            }
            /**************** Unlock Queue bet conclusion lock END ****************/
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Issue in handel"
            );
        }
    }

    /**
     * Preparation of basic information
     *
     * @return bool|string
     * true: Ready to start | false: Minor Error (Needs other information and then it will be called again) | string: Major Error message (It cannot be processed)
     */
    private function init(): bool|string
    {

        try {

            /**************** Set referral session data ****************/
            $setReferralSessionDataRes = $this->setReferralSessionData();

            if ($setReferralSessionDataRes !== true)
                return $setReferralSessionDataRes;
            /**************** Set referral session data END ****************/

            /**************** Collect referred user data ****************/
            /** @var User $referredUser */
            $referredUser = User::find($this->referredClientId);

            if (is_null($referredUser)) return sprintf("Referred user not found!\nReferredClientId: %s", $this->referredClientId);
            if (!$referredUser->isClient()) return sprintf("Referred user is not client!\nReferredClientId: %s", $this->referredClientId);

            /** @var BetconstructClient $referredUserExtra*/
            $referredUserExtra = $referredUser->userExtra;
            if (is_null($referredUserExtra)) {

                FetchClientExtraDataJob::dispatchSync($referredUser->id);
                self::dispatch($this->referredClientId)->delay(now()->addMinutes(2));
                return false;
            }

            /** @var Referral $referredUserReferral */
            $referredUserReferral = $referredUser->clientReferral;
            if (is_null($referredUserReferral)) return sprintf("Referred user does not have referral model data!\nReferredClientId: %s", $this->referredClientId);

            $this->referredUser = $referredUser;
            $this->referredUserExtra = $referredUserExtra;

            $referredCurrency = CurrencyEnum::getCase(strtoupper($referredUserExtra[ClientModelEnum::CurrencyId->dbName()]));

            if (is_null($referredCurrency))
                return sprintf("Referred user currency not detected!\nReferredClientId: %s", $this->referredClientId);

            $this->referredCurrency = $referredCurrency;
            /**************** Collect referred user data END ****************/

            /**************** Collect referrer user data ****************/

            /** @var User $referrerUser */
            $referrerUser = $referredUserReferral->referredByUser;

            if (is_null($referrerUser)) return sprintf("Referrer user not found!\nReferredClientId: %s", $this->referredClientId);
            if (!$referrerUser->isClient()) return sprintf("Referrer user is not client!\nReferredClientId: %s\nReferrerClientId: %s", $this->referredClientId, $referrerUser->id);

            /** @var BetconstructClient $referrerUserExtra*/
            $referrerUserExtra = $referrerUser->userExtra;
            if (is_null($referrerUserExtra)) {

                FetchClientExtraDataJob::dispatchSync($referrerUser->id);
                self::dispatch($this->referredClientId)->delay(now()->addMinutes(2));
                return false;
            }

            $this->referrerUser = $referrerUser;
            $this->referrerUserExtra = $referrerUserExtra;
            $referrerCurrency = CurrencyEnum::getCase(strtoupper($referrerUserExtra[ClientModelEnum::CurrencyId->dbName()]));

            if (is_null($referrerCurrency))
                return sprintf("Referrer user currency not detected!\nReferrerClientId: %s", $referrerUser->id);

            $this->referrerCurrency = $referrerCurrency;
            /**************** Collect referrer user data END ****************/

            /**************** Collect referrral reward package data ****************/
            $identifyRewardPackageRes = $this->identifyRewardPackage();

            if ($identifyRewardPackageRes !== true)
                return $identifyRewardPackageRes;
            /**************** Collect referrral reward package data END ****************/

            /**************** Bets fetch check ****************/
            $referrerBetsFetchCheck = $this->checkClientBetsFetch(true);
            $referredBetsFetchCheck = $this->checkClientBetsFetch(false);

            if (!$referrerBetsFetchCheck || !$referredBetsFetchCheck) {

                self::dispatch($this->referredClientId)->delay(now()->addHours(DelayConstants::ReferralWaitForBetsFetch));
                return false;
            }
            /**************** Bets fetch check END ****************/
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        return true;
    }

    /**
     * Set referral session data
     *
     * @param \App\Enums\Referral\ReferralSessionStatusEnum $referralSessionStatus
     * @return bool|string
     * * true: Ready to start | false: Minor Error (Needs other information and then it will be called again) | string: Major Error message (It cannot be processed)
     */
    private function setReferralSessionData(ReferralSessionStatusEnum $referralSessionStatus = ReferralSessionStatusEnum::PayingRewards): bool|string
    {
        try {
            $statusCol = ReferralSessionsTableEnum::Status->dbName();
            $startedAtCol = ReferralSessionsTableEnum::StartedAt->dbName();
            $finishedAtCol = ReferralSessionsTableEnum::FinishedAt->dbName();

            $payingRewardsStatus = ReferralSessionStatusEnum::PayingRewards;
            $inProgressStatus = ReferralSessionStatusEnum::InProgress;
            $payingRewardsStatusName = $payingRewardsStatus->name;

            $referralSessionStatusName = $referralSessionStatus->name;

            /** @var ReferralSession $referralSession */
            $referralSession = ReferralSession::where($statusCol, $referralSessionStatusName)
                ->orderBy(ReferralSessionsTableEnum::FinishedAt->dbName(), 'asc')
                ->first();

            if (is_null($referralSession)) {

                if ($referralSessionStatusName == $payingRewardsStatusName)
                    return $this->setReferralSessionData($inProgressStatus); // There is no paying reward session, so try for in-progress conclusion
                else
                    return false; // In-progress conclusion has been done, so there is nothing to do
            }

            $this->beginDate = Carbon::parse($referralSession->getRawOriginal($startedAtCol));
            $this->endDate = Carbon::parse($referralSession->getRawOriginal($finishedAtCol));
            $now = now();

            if ($referralSessionStatusName == $payingRewardsStatusName) {
                $this->calculatedUntil = $this->endDate;
            } else {

                // Conclusion is in in-progress status and delay is good to avoid more resource usage;
                $inprogressCalculatedUntil = $this->endDate < $now ? $this->endDate : $now->subHours(DelayConstants::ReferralBetsConclusionInProgressSession);

                $this->calculatedUntil = max($inprogressCalculatedUntil, $this->beginDate);
            }

            // Check if the referral bets conclusion has been done
            $isReferralBetsConclusionDone = ReferralBetsConclusion::where(ReferralBetsConclusionsTableEnum::ReferralSessionId->dbName(), $referralSession->id)
                ->where(ReferralBetsConclusionsTableEnum::ReferredId->dbName(), $this->referredClientId)
                ->where(ReferralBetsConclusionsTableEnum::CalculatedUntil->dbName(), '>=', $this->calculatedUntil)
                ->exists();

            if ($isReferralBetsConclusionDone) {

                if ($referralSessionStatusName == $payingRewardsStatusName)
                    return $this->setReferralSessionData($inProgressStatus); // Paying reward has been calculated, so for in-progress conclusion
                else
                    return false; // In-progress conclusion has been done, so there is nothing to do
            }

            // Verify session for check if dates are loaded correctly or not
            $referralSessionVerify = ReferralSession::where($statusCol, $referralSessionStatusName)
                ->where($startedAtCol, $this->beginDate->toDateTimeString())
                ->where($finishedAtCol, $this->endDate->toDateTimeString())
                ->first();

            if (!is_null($referralSessionVerify)) {

                if ($referralSession->id == $referralSessionVerify->id) {

                    $this->referralSession = $referralSession;

                    return true;
                }
            }

            return sprintf(
                "Faild to verify referral session!\nreferralSession:\n%s\nreferralSessionVerify:\n%s",
                json_encode($referralSession),
                json_encode($referralSessionVerify),
            );
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * Check client bets fetch
     *
     * @param  bool $isReferrerClient true: Referrer Client | false: Referred Client
     * @return bool
     */
    private function checkClientBetsFetch(bool $isReferrerClient): bool
    {
        if ($isReferrerClient) {
            $user = $this->referrerUser;
        } else {
            $user = $this->referredUser;
        }

        $clientSync = $user->clientSync;

        $lastBetsSyncDate = $clientSync[ClientSyncsTableEnum::BetsSyncDate->dbName()];

        if (!empty($lastBetsSyncDate))
            $lastBetsSyncDate = Carbon::parse($lastBetsSyncDate);

        if (empty($lastBetsSyncDate) || $lastBetsSyncDate < $this->calculatedUntil) {
            // The bets of client are not fully synchronized

            $betsSyncStartedAt = $clientSync[ClientSyncsTableEnum::BetsSyncStartedAt->dbName()];

            if (is_null($betsSyncStartedAt)) {
                $clientSync[ClientSyncsTableEnum::BetsSyncStartedAt->dbName()] = now();
                $clientSync->save();

                FetchClientBetsJob::dispatch($user->id);
            }

            return false;
        }

        return true;
    }

    /**
     * Identify referral reward package
     *
     * NOTE:
     * Referral reward package comes from referrer user custom settings
     *
     * @return bool|string string: error message
     */
    private function identifyRewardPackage(): bool|string
    {
        $rewardPackage = null;

        // NOTE: Referral reward package comes from referrer user custom settings
        if ($clientReferralCustomSettings = $this->referrerUser->clientReferralCustomSettings)
            $rewardPackage = $clientReferralCustomSettings->referralRewardPackage;

        if (is_null($rewardPackage))
            $rewardPackage = $this->referralSession->referralRewardPackage;

        if (is_null($rewardPackage))
            return sprintf("Referral reward package not found!\nReferrer User ID: %s", $this->referrerUser->id);

        $this->referralRewardPackage = $rewardPackage;
        return true;
    }

    /**
     * Get min bet amount
     *
     * @return float
     */
    private function getMinBetAmount(): float
    {
        $minBetAmountIrr = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetAmountIrrReferred->dbName()];
        $minBetAmountUsd = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetAmountUsdReferred->dbName()];
        $clientCurrency = $this->referredCurrency;

        return match ($clientCurrency) {

            CurrencyEnum::IRR, CurrencyEnum::TOM, CurrencyEnum::IRT
            => CurrencyEnum::IRR->exchange($minBetAmountIrr, $clientCurrency),

            CurrencyEnum::USD => $minBetAmountUsd,

            default => 0
        };
    }
}
