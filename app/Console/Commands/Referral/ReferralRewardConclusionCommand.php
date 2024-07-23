<?php

namespace App\Console\Commands\Referral;

use App\Constants\DelayConstants;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\JobsTableEnum;
use App\Enums\Database\Tables\ReferralRewardConclusionsTableEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum;
use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\General\QueueEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Jobs\Referral\ReferralRewardConclusionJob;
use App\Models\BackOffice\Referral\Referral;
use App\Models\BackOffice\Referral\ReferralSession;
use App\Models\General\Job;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReferralRewardConclusionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:referral-reward-conclusion-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Referral reward conclusion command';

    private const RECORDS_SYNC_PER_REQUEST = 150;

    private ReferralSession $referralSession;

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

            if (is_null($collection))
                return;

            $idCol = ReferralSessionsTableEnum::Id->dbName();
            $userIdCol = ReferralsTableEnum::UserId->dbName();
            $rewardConclusionQueuedAtCol = ReferralsTableEnum::RewardConclusionQueuedAt->dbName();

            /** @var Referral $referral */
            foreach ($collection as $referral) {

                $referral[$rewardConclusionQueuedAtCol] = now();
                $referral->save();

                ReferralRewardConclusionJob::dispatch($referral[$userIdCol], $this->referralSession[$idCol]);
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
        $rewardConclusionQueuedAtCol = ReferralsTableEnum::RewardConclusionQueuedAt->dbName();

        $jobsCount = Job::where(JobsTableEnum::Queue->dbName(), QueueEnum::ReferralRewardConclusion->value)
            ->count();

        // Min wait to unlock even there is no job to do: DelayConstants::ReferralMinWaitForUnlockTimeoutedJobRecords
        $maxWaitPerJob = 3; // Based on second
        $lockExpireTime = min(now()->subSeconds($jobsCount * $maxWaitPerJob), now()->subMinutes(DelayConstants::ReferralMinWaitForUnlockTimeoutedJobRecords));

        $timeoutItems = Referral::whereNotNull($rewardConclusionQueuedAtCol)
            ->where($rewardConclusionQueuedAtCol, '<', $lockExpireTime)
            ->orderBy($rewardConclusionQueuedAtCol, 'asc')
            ->limit(self::RECORDS_SYNC_PER_REQUEST)
            ->get();

        foreach ($timeoutItems as $item) {

            $item[$rewardConclusionQueuedAtCol] = null;
            $item->save();
        }
    }

    /**
     * Get collection
     *
     * @param  \App\Enums\Referral\ReferralSessionStatusEnum $referralSessionStatus
     * @param  array $finishedSessionIds
     * @return null|\Illuminate\Database\Eloquent\Collection
     */
    private function getCollection(ReferralSessionStatusEnum $referralSessionStatus = ReferralSessionStatusEnum::PayingRewards, array $finishedSessionIds = []): ?Collection
    {
        $referralSessionStatusName = $referralSessionStatus->name;
        $payingRewardsStatusName = ReferralSessionStatusEnum::PayingRewards->name;

        $referralSessionQuery = ReferralSession::where(ReferralSessionsTableEnum::Status->dbName(), $referralSessionStatusName);

        if (!empty($finishedSessionIds))
            $referralSessionQuery->whereNotIn(ReferralSessionsTableEnum::Id->dbName(), $finishedSessionIds);

        /** @var ReferralSession $referralSession */
        $referralSession = $referralSessionQuery
            ->orderBy(ReferralSessionsTableEnum::FinishedAt->dbName(), 'asc')
            ->first();

        if (is_null($referralSession)) {

            if ($referralSessionStatusName == $payingRewardsStatusName)
                return $this->getCollection(ReferralSessionStatusEnum::InProgress);
            else
                return null;
        }

        $referralSessionFinishedAt = Carbon::parse($referralSession->getRawOriginal(ReferralSessionsTableEnum::FinishedAt->dbName()));

        $referralSessionId = $referralSession->id;

        if ($referralSessionStatusName == $payingRewardsStatusName) {
            $maxCalculatedUntil = $referralSessionFinishedAt;
            $isPayingRewardSession = true;
        } else {
            $maxCalculatedUntil = now()->subHours(DelayConstants::ReferralRewardConclusionInProgressSession);
            $isPayingRewardSession = false;
        }

        $referralsTabel = DatabaseTablesEnum::Referrals;
        $referralRewardConclusionsTabel = DatabaseTablesEnum::ReferralRewardConclusions;

        $collection = Referral::has('referrals')
            ->whereNull(ReferralsTableEnum::RewardConclusionQueuedAt->dbName())
            ->where(TimestampsEnum::CreatedAt->dbName(), '<=', $maxCalculatedUntil)

            ->whereNotExists(function ($query) use ($referralsTabel, $referralRewardConclusionsTabel, $referralSessionId, $maxCalculatedUntil, $isPayingRewardSession) {

                // If in in progress session avoid to calculate more than need
                $dateCriteriaColumn = $isPayingRewardSession ? ReferralRewardConclusionsTableEnum::CalculatedUntil->dbNameWithTable($referralRewardConclusionsTabel) : ReferralRewardConclusionsTableEnum::CalculatedAt->dbNameWithTable($referralRewardConclusionsTabel);

                $query->select(DB::raw(1))
                    ->from($referralRewardConclusionsTabel->tableName())
                    ->where(ReferralRewardConclusionsTableEnum::ReferralSessionId->dbNameWithTable($referralRewardConclusionsTabel), $referralSessionId)
                    ->where(function ($query) use ($referralRewardConclusionsTabel, $maxCalculatedUntil, $dateCriteriaColumn) {

                        $query->where(ReferralRewardConclusionsTableEnum::IsDone->dbNameWithTable($referralRewardConclusionsTabel), 1)
                            ->orWhere($dateCriteriaColumn, '>=', $maxCalculatedUntil);
                    })
                    ->whereColumn(ReferralRewardConclusionsTableEnum::UserId->dbNameWithTable($referralRewardConclusionsTabel), ReferralsTableEnum::UserId->dbNameWithTable($referralsTabel));
            })
            ->orderBy(TimestampsEnum::CreatedAt->dbNameWithTable($referralsTabel))
            ->limit(self::RECORDS_SYNC_PER_REQUEST)
            ->get();

        if ($collection->isEmpty()) {
            // This session is done, so try to do next session conclusion

            if ($referralSession[ReferralSessionsTableEnum::Status->dbName()] == $payingRewardsStatusName)
                $this->checkPayingRewardSessionStatus($referralSession);

            array_push($finishedSessionIds, $referralSessionId);
            return $this->getCollection($referralSessionStatus, $finishedSessionIds);
        }

        $this->referralSession = $referralSession;

        return $collection;
    }

    /**
     * Check session with "PayingReward" status
     *
     * @return void
     */
    private function checkPayingRewardSessionStatus(ReferralSession $referralSession): void
    {
        if ($referralSession[ReferralSessionsTableEnum::Status->dbName()] == ReferralSessionStatusEnum::PayingRewards->name) {

            $referralSessionFinishedAt = $referralSession->getRawOriginal(ReferralSessionsTableEnum::FinishedAt->dbName());

            $referralsTabel = DatabaseTablesEnum::Referrals;
            $referralRewardConclusionsTabel = DatabaseTablesEnum::ReferralRewardConclusions;

            $collection = Referral::has('referrals')
                ->where(TimestampsEnum::CreatedAt->dbName(), '<=', $referralSessionFinishedAt)

                ->whereNotExists(function ($query) use ($referralsTabel, $referralRewardConclusionsTabel, $referralSession) {

                    $query->select(DB::raw(1))
                        ->from($referralRewardConclusionsTabel->tableName())
                        ->where(ReferralRewardConclusionsTableEnum::ReferralSessionId->dbNameWithTable($referralRewardConclusionsTabel), $referralSession->id)
                        ->where(ReferralRewardConclusionsTableEnum::IsDone->dbNameWithTable($referralRewardConclusionsTabel), 1)
                        ->whereColumn(ReferralRewardConclusionsTableEnum::UserId->dbNameWithTable($referralRewardConclusionsTabel), ReferralsTableEnum::UserId->dbNameWithTable($referralsTabel));
                });

            if ($collection->count() == 0) {
                // All payments for the referral session have been made

                $referralSession[ReferralSessionsTableEnum::Status->dbName()] = ReferralSessionStatusEnum::Finished->name;
                $referralSession->save();
            }
        }
    }
}
