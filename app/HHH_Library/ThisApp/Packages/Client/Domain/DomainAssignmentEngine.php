<?php

namespace App\HHH_Library\ThisApp\Packages\Client\Domain;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum;
use App\Enums\Database\Tables\DomainCategoriesTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\ThisApp\Packages\Client\TrustScore\ClientTrustScoreEngine;
use App\Models\BackOffice\Domains\AssignedDomain;
use App\Models\BackOffice\Domains\Domain;
use App\Models\BackOffice\Domains\DomainCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DomainAssignmentEngine
{
    private ?User $user;
    private ?Domain $domain = null;

    private $clientTrustScore = -1;
    private $clientDomainSuspiciousScore = 0;
    private $isClientSuspicious = true;

    /**
     * __construct
     *
     * @return void
     */
    function __construct(?User $user = null)
    {
        $this->user = is_null($user) ? auth()->user() : $user;

        if (!is_null($this->user)) {

            $clientTrustScoreEngine = new ClientTrustScoreEngine();
            $this->clientTrustScore = $clientTrustScoreEngine->getTrustScore();
            $this->isClientSuspicious = $clientTrustScoreEngine->isClientSuspicious();
            $this->clientDomainSuspiciousScore = $clientTrustScoreEngine->getDomainSuspiciousScore();
        }
    }

    /**
     * Get site Url
     *
     * @return string
     */
    public  function getSiteUrl(bool $isDesktop = true): string
    {
        return $isDesktop ? sprintf('https://www.%s/fa/', $this->getDomainName())
            : sprintf('https://m.%s/fa/', $this->getDomainName());
    }

    /**
     * Get domain name
     *
     * @return string
     */
    public function getDomainName(): string
    {
        $domain = $this->getDomain();

        return is_null($domain) ? AppTechnicalSettingsEnum::DoAsSy_PermanentDomain->getValue() : $domain[DomainsTableEnum::Name->dbName()];
    }

    /**
     * Get site domain
     *
     * @return null|\App\Models\BackOffice\Domains\Domain
     */
    public function getDomain(): ?Domain
    {
        if (is_null($this->user))
            return null;

        if (!is_null($this->domain))
            return $this->domain;

        $domain = null; //default

        // Check last assigned domain
        $assignedDomain = $this->getAssignedDomain();

        if (is_null($assignedDomain)) {
            // Try to assign new domain

            $domain = $this->assignDedicatedDomain();

            if (is_null($domain))
                $domain = $this->assignPublicDomain();
        } else {

            if ($assignedDomain[AssignedDomainsTableEnum::Reported->dbName()] && $this->isClientHasTrustScore()) {
                $domain = $this->assignPublicDomain();
            } else
                $domain = $assignedDomain->domain;
        }

        $this->domain = $domain;
        return $domain;
    }

    /**
     * Fetch domain from category
     *
     * @return ?Domain
     */
    public static function fetchFreshDomain(): ?Domain
    {
        $domain = Domain::whereIn(DomainsTableEnum::DomainCategoryId->dbName(), self::getDomainAssigningCategoryIds())
            ->where(DomainsTableEnum::Status->dbName(), DomainStatusEnum::ReadyToUse->name)
            ->orderBy(DomainsTableEnum::RegisteredAt->dbName(), 'asc')
            ->orderBy(DomainsTableEnum::Id->dbName(), 'asc')
            ->first();

        return $domain;
    }

    /**
     * Get domain assigning category IDs
     * Categories of domains that have the possibility of assigning a domain
     *
     * @return array
     */
    public static function getDomainAssigningCategoryIds(): array
    {
        try {

            return DomainCategory::where(DomainCategoriesTableEnum::DomainAssignment->dbName(), 1)
                ->where(DomainCategoriesTableEnum::IsActive->dbName(), 1)
                ->orderBy(DomainCategoriesTableEnum::Id->dbName(), 'asc')
                ->get()
                ->pluck(DomainCategoriesTableEnum::Id->dbName())
                ->toArray();
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Check if the client has trust score
     *
     * @return bool
     */
    private function isClientHasTrustScore(): bool
    {
        return $this->clientTrustScore > 0 ? true : false;
    }

    /**
     * Get last assigned domain
     *
     * @return null|\App\Models\BackOffice\Domains\AssignedDomain
     */
    private function getAssignedDomain(): ?AssignedDomain
    {
        if (is_null($this->user))
            return null;

        $domainsTable = DatabaseTablesEnum::Domains;
        $assignedDomainsTable = DatabaseTablesEnum::AssignedDomains;

        if ($this->isClientHasTrustScore()) {
            // Get last assigned dedicated domain

            /** @var AssignedDomain $assignedDomain */
            $assignedDomain = AssignedDomain::leftJoin($domainsTable->tableName(), DomainsTableEnum::Id->dbNameWithTable($domainsTable), '=', AssignedDomainsTableEnum::DomainId->dbNameWithTable($assignedDomainsTable))
                ->where(AssignedDomainsTableEnum::UserId->dbNameWithTable($assignedDomainsTable), $this->user->id)
                ->where(DomainsTableEnum::Public->dbNameWithTable($domainsTable), 0)
                ->where(DomainsTableEnum::Status->dbNameWithTable($domainsTable), DomainStatusEnum::InUse->name)
                ->select([
                    $assignedDomainsTable->tableName() . '.*',
                ])
                ->orderBy(TimestampsEnum::UpdatedAt->dbNameWithTable($assignedDomainsTable), 'desc')
                ->orderBy(AssignedDomainsTableEnum::Id->dbNameWithTable($assignedDomainsTable), 'desc')
                ->first();
        } else {
            // Get last assigned domain to untrusted user

            $assignedDomain = AssignedDomain::where(AssignedDomainsTableEnum::UserId->dbName(), $this->user->id)
                ->orderBy(TimestampsEnum::UpdatedAt->dbNameWithTable($assignedDomainsTable), 'desc')
                ->orderBy(AssignedDomainsTableEnum::Id->dbNameWithTable($assignedDomainsTable), 'desc')
                ->first();
        }

        if ($this->isAssignedDomainUsable($assignedDomain))
            return $assignedDomain;

        return null;
    }

    /**
     * Check if assigned domain usable
     *
     * @param  \App\Models\BackOffice\Domains\AssignedDomain $assignedDomain
     * @return bool
     */
    private function isAssignedDomainUsable(?AssignedDomain $assignedDomain): bool
    {
        if (is_null($assignedDomain))
            return false;

        $fakeAssigned = $assignedDomain[AssignedDomainsTableEnum::FakeAssigned->dbName()];

        $domain = $assignedDomain->domain;

        if ($this->isClientHasTrustScore()) {

            if (!$domain->isDomainUsable())
                return false;
        } else {
            // Change the domain of untrusted client

            if ($assignedDomain[TimestampsEnum::CreatedAt->dbName()] < Carbon::now()->subHours(5))
                return false;
        }

        if (!$fakeAssigned && $assignedDomain[AssignedDomainsTableEnum::Reported->dbName()]) {
            // The domain was reported by the client

            $domainReportsCount = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $assignedDomain[AssignedDomainsTableEnum::DomainId->dbName()])
                ->where(AssignedDomainsTableEnum::UserId->dbName(), '!=', $assignedDomain[AssignedDomainsTableEnum::UserId->dbName()])
                ->where(AssignedDomainsTableEnum::Reported->dbName(), 1)
                ->groupBy(AssignedDomainsTableEnum::UserId->dbName())
                ->distinct()
                ->count();

            if ($domainReportsCount > AppTechnicalSettingsEnum::DoAsSy_MinReportCount->getValue())
                return false;

            if ($this->clientTrustScore > 80)
                $hoursLimit = 6;
            else if ($this->clientTrustScore > 50)
                $hoursLimit = 12;
            else
                $hoursLimit = 24;

            $hoursLimitExpire = Carbon::now()->subHours($hoursLimit);

            if ($domain[DomainsTableEnum::AnnouncedAt->dbName()] < $hoursLimitExpire)
                return false;
        }

        return true;
    }

    /**
     * Assign dedicated domain
     *
     * @return null|\App\Models\BackOffice\Domains\Domain
     */
    private function assignDedicatedDomain(): ?Domain
    {
        // This client is not trusted, so there is no need to assign a unblocked domain
        if (!$this->isClientHasTrustScore())
            return $this->assignBlockedDomain();

        try {

            if ($this->clientTrustScore < AppTechnicalSettingsEnum::DoAsSy_MinAssignableTrustScore->getValue())
                return null;

            if ($this->isClientSuspicious)
                return null;

            $domain = null;

            list($minTrustScore, $maxTrustScore) = $this->getTrustScoreRange();

            // Last assigned domain to same trust score
            $domainsTable = DatabaseTablesEnum::Domains;
            $assignedDomainsTable = DatabaseTablesEnum::AssignedDomains;

            /** @var AssignedDomain $assignedDomain */
            $assignedDomains = AssignedDomain::leftJoin($domainsTable->tableName(), DomainsTableEnum::Id->dbNameWithTable($domainsTable), '=', AssignedDomainsTableEnum::DomainId->dbNameWithTable($assignedDomainsTable))
                ->where(AssignedDomainsTableEnum::ClientTrustScore->dbNameWithTable($assignedDomainsTable), '>', $minTrustScore)
                ->where(AssignedDomainsTableEnum::ClientTrustScore->dbNameWithTable($assignedDomainsTable), '<=', $maxTrustScore)
                ->where(DomainsTableEnum::Public->dbNameWithTable($domainsTable), 0)
                ->where(DomainsTableEnum::Status->dbNameWithTable($domainsTable), DomainStatusEnum::InUse->name)
                ->select([
                    $assignedDomainsTable->tableName() . '.*',
                ])
                ->groupBy(AssignedDomainsTableEnum::DomainId->dbNameWithTable($assignedDomainsTable))
                ->distinct()
                ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'asc')
                ->orderBy(AssignedDomainsTableEnum::Id->dbName(), 'desc')
                ->get();

            // Find unreported domain by client
            foreach ($assignedDomains as $assignedDomain) {

                $isDomainAssignedBefore = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $assignedDomain[AssignedDomainsTableEnum::DomainId->dbName()])
                    ->where(AssignedDomainsTableEnum::UserId->dbName(), $this->user->id)
                    ->where(AssignedDomainsTableEnum::Reported->dbName(), 1)
                    ->exists();

                if (!$isDomainAssignedBefore) {
                    $domain = $assignedDomain->domain;
                    break;
                }
            }

            // Get new domain from category
            if (is_null($domain))
                $domain = self::fetchFreshDomain();

            if (!is_null($domain)) {

                if ($domain[DomainsTableEnum::Status->dbName()] == DomainStatusEnum::ReadyToUse->name) {
                    // New domain is assigning
                    $domain->fill([
                        DomainsTableEnum::Status->dbName()      => DomainStatusEnum::InUse->name,
                        DomainsTableEnum::Public->dbName()      => 0,
                        DomainsTableEnum::Suspicious->dbName()  => 0,
                        DomainsTableEnum::AnnouncedAt->dbName() => Carbon::now(),
                    ]);
                    $domain->save();
                }

                return $this->registerDomainInAssignedDomains($domain) ? $domain : null;
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                'New domain assignment issue'
            );
        }

        return null;
    }

    /**
     * Assign public domain
     *
     * For a client that is not trusted,
     * there is no need to assign an unblocked domain.
     *
     * @return null|\App\Models\BackOffice\Domains\Domain
     */
    private function assignPublicDomain(bool $assignSucpiciousDomain = false): ?Domain
    {
        // This client is not trusted, so there is no need to assign a unblocked domain
        if (!$this->isClientHasTrustScore())
            return $this->assignBlockedDomain();

        if (!$assignSucpiciousDomain && $this->isClientSuspicious)
            return $this->assignPublicDomain(true);

        try {
            $domain = null;

            $lastPublicDomains = Domain::where(DomainsTableEnum::Public->dbName(), 1)
                ->where(DomainsTableEnum::Suspicious->dbName(), $assignSucpiciousDomain)
                ->where(DomainsTableEnum::Status->dbName(), DomainStatusEnum::InUse->name)
                ->orderBy(DomainsTableEnum::Id->dbName(), 'asc')
                ->get();

            $minPublicDomainReportsCount = AppTechnicalSettingsEnum::DoAsSy_MinPublicDomainReportsCount->getValue();
            $minPublicDomainHoldMinutes = AppTechnicalSettingsEnum::DoAsSy_MinPublicDomainHoldMinutes->getValue();

            if (!is_null($minPublicDomainHoldMinutes))
                $holdTimeExpire = Carbon::now()->subMinutes($minPublicDomainHoldMinutes);

            foreach ($lastPublicDomains as $lastPublicDomain) {

                if ($lastPublicDomain[DomainsTableEnum::Reported->dbName()]) {
                    // Check domain report counts

                    $reportsCount = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $lastPublicDomain[DomainsTableEnum::Id->dbName()])
                        ->where(AssignedDomainsTableEnum::Reported->dbName(), 1)
                        ->count();

                    if ($reportsCount > $minPublicDomainReportsCount) {

                        if (!is_null($minPublicDomainHoldMinutes)) {
                            // Check minimum public domain hold time

                            if ($lastPublicDomain[DomainsTableEnum::AnnouncedAt->dbName()] > $holdTimeExpire) {

                                $domain = $lastPublicDomain;
                                break;
                            }
                        }
                    } else {
                        $domain = $lastPublicDomain;
                        break;
                    }
                } else {
                    $domain = $lastPublicDomain;
                    break;
                }
            }

            // Assign new public domain
            if (is_null($domain))
                $domain = $this->fetchFreshDomain();
            else if (!$assignSucpiciousDomain) {
                // Check if the client reported the non-suspicious domain

                $isClientReported = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $domain->id)
                    ->where(AssignedDomainsTableEnum::UserId->dbName(), $this->user->id)
                    ->where(AssignedDomainsTableEnum::Reported->dbName(), 1)
                    ->exists();

                if ($isClientReported)
                    return $this->assignPublicDomain(true);
            }

            if (!is_null($domain)) {

                if ($domain[DomainsTableEnum::Status->dbName()] == DomainStatusEnum::ReadyToUse->name) {
                    // New domain is assigning
                    $domain->fill([
                        DomainsTableEnum::Status->dbName()      => DomainStatusEnum::InUse->name,
                        DomainsTableEnum::Public->dbName()      => 1,
                        DomainsTableEnum::Suspicious->dbName()  => $assignSucpiciousDomain,
                        DomainsTableEnum::AnnouncedAt->dbName() => Carbon::now(),
                    ]);
                    $domain->save();
                }

                return $this->registerDomainInAssignedDomains($domain) ? $domain : null;
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                'New public domain assignment issue'
            );
        }

        return null;
    }

    /**
     * Assign blocked domain
     *
     * For a client that is not trusted,
     * there is no need to assign an unblocked domain.
     *
     * @return null|\App\Models\BackOffice\Domains\Domain
     */
    private function assignBlockedDomain(): ?Domain
    {
        $domainsTable = DatabaseTablesEnum::Domains;
        $assignedDomainsTable = DatabaseTablesEnum::AssignedDomains;

        $assignedDomainToClientQuery = DB::table($assignedDomainsTable->tableName())
            ->select(DB::raw(1))
            ->whereColumn(DomainsTableEnum::Id->dbNameWithTable($domainsTable), AssignedDomainsTableEnum::DomainId->dbNameWithTable($assignedDomainsTable))
            ->where(AssignedDomainsTableEnum::UserId->dbNameWithTable($assignedDomainsTable), '=', $this->user->id);

        $domain = Domain::whereIn(DomainsTableEnum::DomainCategoryId->dbName(), self::getDomainAssigningCategoryIds())
            ->where(DomainsTableEnum::Status->dbName(), DomainStatusEnum::Blocked->name)
            ->where(DomainsTableEnum::ExpiresAt->dbName(), '>', Carbon::now()->addDays(3))
            ->whereNotExists($assignedDomainToClientQuery)
            ->orderBy(DomainsTableEnum::RegisteredAt->dbName(), 'desc')
            ->orderBy(DomainsTableEnum::Id->dbName(), 'desc')
            ->first();

        return $this->registerDomainInAssignedDomains($domain, true) ? $domain : null;
    }

    /**
     * Get client trust score range
     *
     * @return array
     */
    private function getTrustScoreRange(): array
    {
        $clientTrustScore = $this->clientTrustScore;

        $min = 1;
        $max = 100;

        $maxAssignableDomains = AppTechnicalSettingsEnum::DoAsSy_MaxAssignableDomains->getValue();
        $minAssignableTrustScore = AppTechnicalSettingsEnum::DoAsSy_MinAssignableTrustScore->getValue();

        $assignableTrustscorseCount = 100 - $minAssignableTrustScore + 1;

        $trustScoreRange = (int) number_format($assignableTrustscorseCount / $maxAssignableDomains, 0, '.', '');

        if ($trustScoreRange < 1)
            $trustScoreRange = 1;

        for ($i = $minAssignableTrustScore - 1; $i < 101; $i += $trustScoreRange) {

            $min = $i;
            $max = $min + $trustScoreRange;
            if ($clientTrustScore > $min && $clientTrustScore <= $max)
                break;
        }

        return [$min, $max];
    }

    /**
     * Register domain in assigned domains
     *
     * @param null|\App\Models\BackOffice\Domains\Domain $domain
     * @param bool $fakeAssigned
     * @return bool
     */
    private function registerDomainInAssignedDomains(?Domain $domain, bool $fakeAssigned = false): bool
    {

        if (is_null($domain))
            return false;

        try {

            $assignedDomain = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $domain[DomainsTableEnum::Id->dbName()])
                ->where(AssignedDomainsTableEnum::UserId->dbName(), $this->user->id)
                ->first();

            if (is_null($assignedDomain))
                $assignedDomain = new AssignedDomain();

            $assignedDomain->forceFill([
                AssignedDomainsTableEnum::UserId->dbName()                  => $this->user->id,
                AssignedDomainsTableEnum::DomainId->dbName()                => $domain->id,
                AssignedDomainsTableEnum::ClientTrustScore->dbName()        => $this->clientTrustScore,
                AssignedDomainsTableEnum::DomainSuspiciousScore->dbName()   => $this->clientDomainSuspiciousScore,
                AssignedDomainsTableEnum::FakeAssigned->dbName()            => $fakeAssigned,
                TimestampsEnum::UpdatedAt->dbName()                         => Carbon::now(), // Necessary for use in "getAssignedDomain" function
            ]);

            $assignedDomain->save();
            return true;
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                'Saving new assigned domain issue'
            );
        }

        return false;
    }
}
