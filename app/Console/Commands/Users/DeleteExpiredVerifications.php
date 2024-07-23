<?php

namespace App\Console\Commands\Users;

use App\Enums\Database\Tables\VerificationsTableEnum;
use App\Models\General\Verification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredVerifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:delete-expired-verifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired verifications.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $validUntilCol = VerificationsTableEnum::ValidUntil->dbName();

        Verification::where($validUntilCol, '<', Carbon::now()->subDays(2)) // Keep unsed verifications untill 2 days
            ->orWhereNull($validUntilCol)
            ->delete();
    }
}
