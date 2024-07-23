<?php

namespace App\Console\Commands\Domains;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\Models\BackOffice\Domains\AssignedDomain;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredAssignedDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:delete-expired-assigned-domains';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired assigned domains.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expireDate = Carbon::now()->subDays(AppTechnicalSettingsEnum::DoAsSy_DaysOfKeepingExipredAssignments->getValue());


        $domainsTable = DatabaseTablesEnum::Domains;
        $assignedDomainsTable = DatabaseTablesEnum::AssignedDomains;

        AssignedDomain::leftJoin($domainsTable->tableName(), DomainsTableEnum::Id->dbNameWithTable($domainsTable), '=', AssignedDomainsTableEnum::DomainId->dbNameWithTable($assignedDomainsTable))
            ->whereIn(DomainsTableEnum::Status->dbNameWithTable($domainsTable), DomainStatusEnum::getAssignmentExpiredStatusNames())
            ->where(TimestampsEnum::UpdatedAt->dbNameWithTable($assignedDomainsTable), '<',  $expireDate)
            ->select(
                $assignedDomainsTable->tableName() . ".*",
            )
            ->delete();
    }
}
