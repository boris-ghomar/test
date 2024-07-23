<?php

namespace App\Console\Commands\Domains;

use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\Models\BackOffice\Domains\Domain;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:check-expired-domains';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check expired domains.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $statusCol = DomainsTableEnum::Status->dbName();
        $expiresAtCol = DomainsTableEnum::ExpiresAt->dbName();
        $autoRenewCol = DomainsTableEnum::AutoRenew->dbName();

        $expiredDomains = Domain::where($statusCol, '!=', DomainStatusEnum::Expired->name)
            ->where($expiresAtCol, '<', Carbon::now())
            ->get();

        foreach ($expiredDomains as $domain) {

            if ($domain->$autoRenewCol) {

                $expiresAt = $domain->$expiresAtCol;
                $domain[$expiresAtCol] = Carbon::parse($expiresAt)->addYear();
            } else {
                $domain[$statusCol] = DomainStatusEnum::Expired->name;
            }

            $domain->save();
        }
    }
}
