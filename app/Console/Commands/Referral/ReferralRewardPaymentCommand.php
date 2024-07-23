<?php

namespace App\Console\Commands\Referral;

use App\Constants\DelayConstants;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\JobsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum;
use App\Enums\General\QueueEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Jobs\Referral\ReferralRewardPaymentJob;
use App\Models\BackOffice\Referral\ReferralRewardPayment;
use App\Models\General\Job;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class ReferralRewardPaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:referral-reward-payment-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Referral reward payment command';

    private const RECORDS_SYNC_PER_REQUEST = 150;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isAppActive = AppSettingsEnum::IsCommunityActive->getValue();
        $isReferralActive = AppSettingsEnum::ReferralIsActive->getValue() || AppSettingsEnum::ReferralIsActiveForTestClients->getValue();

        $isAllowedToRun = $isAppActive && $isReferralActive;

        if ($isAllowedToRun) {

            $this->unlockTimeoutedRecords();

            $collection = $this->getCollection();

            $idCol = ReferralRewardPaymentsTableEnum::Id->dbName();
            $queuedAtCol = ReferralRewardPaymentsTableEnum::QueuedAt->dbName();

            $queuedAt = now()->toDateTimeString();

            /** @var ReferralRewardPayment $referralRewardPayment */
            foreach ($collection as $referralRewardPayment) {

                ReferralRewardPaymentJob::dispatch($referralRewardPayment[$idCol]);

                $referralRewardPayment[$queuedAtCol] = $queuedAt;
                $referralRewardPayment->save();
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
        $queuedAtCol = ReferralRewardPaymentsTableEnum::QueuedAt->dbName();

        $jobsCount = Job::where(JobsTableEnum::Queue->dbName(), QueueEnum::ReferralRewardPayment->value)
            ->count();

        // Min wait to unlock even there is no job to do: DelayConstants::ReferralMinWaitForUnlockTimeoutedJobRecords
        $maxWaitPerJob = 5; // Based on second
        $lockExpireTime = min(now()->subSeconds($jobsCount * $maxWaitPerJob), now()->subMinutes(DelayConstants::ReferralMinWaitForUnlockTimeoutedJobRecords));

        $timeoutItems = ReferralRewardPayment::whereNotNull($queuedAtCol)
            ->where($queuedAtCol, '<', $lockExpireTime)
            ->orderBy($queuedAtCol, 'asc')
            ->limit(self::RECORDS_SYNC_PER_REQUEST)
            ->get();

        foreach ($timeoutItems as $item) {

            $item[$queuedAtCol] = null;
            $item->save();
        }
    }

    /**
     * Get collection
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getCollection(): Collection
    {
        $collection = ReferralRewardPayment::whereNull(ReferralRewardPaymentsTableEnum::QueuedAt->dbName())
            ->where(ReferralRewardPaymentsTableEnum::IsDone->dbName(), 0)
            ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'asc')
            ->limit(self::RECORDS_SYNC_PER_REQUEST)
            ->get();

        return $collection;
    }
}
