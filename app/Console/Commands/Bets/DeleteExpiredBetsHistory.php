<?php

namespace App\Console\Commands\Bets;

use App\Enums\Bets\BetStatusEnum;
use App\Enums\Database\Tables\BetsTableEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Models\BackOffice\Bets\Bet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class DeleteExpiredBetsHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:delete-expired-history-of-bets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired history of bets.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expireDate = Carbon::now()->subDays(AppSettingsEnum::BetDaysOfKeepingHistory->getValue());

        Bet::where(BetsTableEnum::Status->dbName(), '!=', BetStatusEnum::Accepted->name)
            ->where(function (Builder $query) use ($expireDate) {

                $query->whereNull(BetsTableEnum::PlacedAt->dbName())
                    ->orWhere(BetsTableEnum::PlacedAt->dbName(), '<', $expireDate);
            })
            ->where(function (Builder $query) use ($expireDate) {

                $query->whereNotNull(BetsTableEnum::CalculatedAt->dbName())
                    ->where(BetsTableEnum::CalculatedAt->dbName(), '<', $expireDate);
            })
            ->delete();
    }
}
