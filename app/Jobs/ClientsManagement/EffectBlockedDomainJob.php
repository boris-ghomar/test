<?php

namespace App\Jobs\ClientsManagement;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum;
use App\Enums\Database\Tables\ClientTrustScoresTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Models\BackOffice\Domains\AssignedDomain;
use App\Models\BackOffice\Domains\Domain;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EffectBlockedDomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?Domain $domain;

    /**
     * Create a new job instance.
     *
     * @param null|\App\Models\BackOffice\Domains\Domain $domain
     */
    public function __construct(?Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $domain = $this->domain;

            if (is_null($domain))
                return;

            /********** Database columns **********/
            $trSc_scoreCol = ClientTrustScoresTableEnum::Score->dbName();
            $trSc_domainSuspiciousCol = ClientTrustScoresTableEnum::DomainSuspicious->dbName();

            $asDo_domainIdCol =  AssignedDomainsTableEnum::DomainId->dbName();
            $asDo_userIdCol =  AssignedDomainsTableEnum::UserId->dbName();
            $asDo_fakeAssignedCol =  AssignedDomainsTableEnum::FakeAssigned->dbName();
            $asDo_reportedAtCol =  AssignedDomainsTableEnum::ReportedAt->dbName();

            $createdAtCol =  TimestampsEnum::CreatedAt->dbName();
            /********** Database columns END **********/

            $negativePointValue = AppTechnicalSettingsEnum::TrScSy_NegativePointValue->getValue();

            $firstReport = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $domain[DomainsTableEnum::Id->dbName()])
                ->where(AssignedDomainsTableEnum::Reported->dbName(), 1)
                ->orderBy(AssignedDomainsTableEnum::ReportedAt->dbName(), 'asc')
                ->first();

            $firstReportDate = is_null($firstReport) ? Carbon::now() : $firstReport[AssignedDomainsTableEnum::ReportedAt->dbName()];

            $negativePointOffset = Carbon::parse($firstReportDate)->subHour();
            $positivePointOffset = Carbon::parse($negativePointOffset)->subMinutes(30);

            $assignedUsers = $domain->assignedUsers;

            foreach ($assignedUsers as $assignedUser) {

                /** @var ClientTrustScore $clientTrustScore */
                $clientTrustScore = $assignedUser->clientTrustScore;

                if (!is_null($clientTrustScore)) {

                    $assignedDomain = AssignedDomain::where($asDo_domainIdCol, $domain->id)
                        ->where($asDo_userIdCol, $assignedUser->id)
                        ->where($asDo_fakeAssignedCol, 0)
                        ->orderBy('id', 'desc')
                        ->first();

                    $assignedAt = $assignedDomain->$createdAtCol;

                    if ($assignedAt >  $negativePointOffset) {
                        /**
                         * The negative score of the blocked domain is given to users
                         * who have received the domain for less than a expire time.
                         */
                        $clientTrustScore->decrement($trSc_scoreCol, $negativePointValue);
                        $clientTrustScore->increment($trSc_domainSuspiciousCol, 1);
                    } else if ($assignedAt < $positivePointOffset) {
                        $clientTrustScore->decrement($trSc_domainSuspiciousCol, 1);
                    }

                    /**
                     * If the client reports the domain less than a minute after assignment,
                     * it is suspicious
                     */
                    $reportedAt = $assignedDomain->$asDo_reportedAtCol;

                    if (!is_null($reportedAt)) {

                        $reportOffset = Carbon::parse($reportedAt)->subMinute();
                        if ($assignedAt > $reportOffset)
                            $clientTrustScore->increment($trSc_domainSuspiciousCol, 1);
                    }
                }
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Effect blocked domain job issue"
            );
        }
    }
}
