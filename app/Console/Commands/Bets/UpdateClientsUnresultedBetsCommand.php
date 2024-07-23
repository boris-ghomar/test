<?php

namespace App\Console\Commands\Bets;

use App\Constants\DelayConstants;
use App\Enums\Bets\BetStatusEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\BetsTableEnum;
use App\Enums\Database\Tables\JobsTableEnum;
use App\Enums\General\QueueEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Jobs\FetchData\Single\UpdateBetJob;
use App\Models\BackOffice\Bets\Bet;
use App\Models\General\Job;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class UpdateClientsUnresultedBetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:update-clients-unresulted-bets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update clients unresulted bets.';

    private const SYNC_DELAY = 5; // Based on hours
    private const RECORDS_SYNC_PER_REQUEST = 150;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (AppSettingsEnum::IsCommunityActive->getValue()) {

            $this->unlockTimeoutedRecords();

            $bets = $this->getBets();

            $isQueuedCol = BetsTableEnum::IsQueued->dbName();

            /** @var Bet $bet */
            foreach ($bets as $bet) {

                $bet[$isQueuedCol] = 1;
                $bet->save();

                UpdateBetJob::dispatch($bet->id);
            }
        }
    }

    /**
     * Unlock timeouted records
     *
     * @return void
     */
    private function unlockTimeoutedRecords(): void
    {
        $isQueuedCol = BetsTableEnum::IsQueued->dbName();
        $updatedAtCol = TimestampsEnum::UpdatedAt->dbName();

        $jobsCount = Job::where(JobsTableEnum::Queue->dbName(), QueueEnum::UpdateClientUnresultedBets->value)
            ->count();

        // Min wait to unlock even there is no job to do: DelayConstants::ReferralMinWaitForUnlockTimeoutedJobRecords
        $maxWaitPerJob = 2; // Based on second
        $syncWaitExpireTime = min(now()->subSeconds($jobsCount * $maxWaitPerJob), now()->subMinutes(DelayConstants::ReferralMinWaitForUnlockTimeoutedJobRecords));

        $timeoutItems = Bet::where($isQueuedCol, 1)
            ->where($updatedAtCol, '<', $syncWaitExpireTime)
            ->orderBy($updatedAtCol, 'asc')
            ->limit(self::RECORDS_SYNC_PER_REQUEST)
            ->get();

        foreach ($timeoutItems as $bet) {

            $lastUpdateAt = Carbon::parse($bet[$updatedAtCol])->addMinute()->toDateTimeString();

            $bet[$isQueuedCol] = 0;
            $bet[$updatedAtCol] =  $lastUpdateAt;
            $bet->save();
        }
    }

    /**
     * Get bets
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getBets(): Collection
    {
        $syncDelay = now()->subHours(self::SYNC_DELAY);

        $bets = Bet::where(BetsTableEnum::Status->dbName(), BetStatusEnum::Accepted->name)
            ->where(BetsTableEnum::IsQueued->dbName(), 0)
            ->where(TimestampsEnum::UpdatedAt->dbName(), '<', $syncDelay)
            ->limit(self::RECORDS_SYNC_PER_REQUEST)
            ->get();

        return $bets;
    }
}
