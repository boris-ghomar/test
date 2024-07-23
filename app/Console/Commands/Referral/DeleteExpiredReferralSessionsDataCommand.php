<?php

namespace App\Console\Commands\Referral;

use App\Enums\Database\Tables\ReferralSessionsTableEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\Models\BackOffice\Referral\ReferralSession;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredReferralSessionsDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:delete-expired-referral-sessions-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired referral sessions data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expireDate = Carbon::now()->subDays(90);

        ReferralSession::where(ReferralSessionsTableEnum::FinishedAt->dbName(), '<', $expireDate)
            ->where(ReferralSessionsTableEnum::Status->dbName(), ReferralSessionStatusEnum::Finished->name)
            ->withTrashed()
            ->forceDelete();
    }
}
