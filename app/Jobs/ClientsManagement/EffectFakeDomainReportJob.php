<?php

namespace App\Jobs\ClientsManagement;

use App\Enums\Database\Tables\AssignedDomainsTableEnum;
use App\Enums\Database\Tables\ClientTrustScoresTableEnum;
use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Models\BackOffice\Domains\AssignedDomain;
use App\Models\BackOffice\Domains\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EffectFakeDomainReportJob implements ShouldQueue
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

            $assignedDomains = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $domain->id)
                ->where(AssignedDomainsTableEnum::Reported->dbName(), 1)
                ->get();

            $negativePointValue = AppTechnicalSettingsEnum::TrScSy_NegativePointValue->getValue();

            /********** Database columns **********/
            $trSc_scoreCol = ClientTrustScoresTableEnum::Score->dbName();
            $trSc_domainSuspiciousCol = ClientTrustScoresTableEnum::DomainSuspicious->dbName();

            $asDo_reportedCol =  AssignedDomainsTableEnum::Reported->dbName();
            $asDo_reportedAtCol =  AssignedDomainsTableEnum::ReportedAt->dbName();
            /********** Database columns END **********/

            foreach ($assignedDomains as $assignedDomain) {

                $assignedDomain[$asDo_reportedCol] = 0;
                $assignedDomain[$asDo_reportedAtCol] = null;
                $assignedDomain->save();

                $clientTrustScore = $assignedDomain->user->clientTrustScore;

                if (!is_null($clientTrustScore)) {
                    $clientTrustScore->decrement($trSc_scoreCol, $negativePointValue);
                    $clientTrustScore->increment($trSc_domainSuspiciousCol, 1);
                }
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Effect fake domain report job issue"
            );
        }
    }
}
