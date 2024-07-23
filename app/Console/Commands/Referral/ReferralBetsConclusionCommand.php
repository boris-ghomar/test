<?php

namespace App\Console\Commands\Referral;

use App\Constants\DelayConstants;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\JobsTableEnum;
use App\Enums\Database\Tables\ReferralBetsConclusionsTableEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum;
use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\General\QueueEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Jobs\Referral\ReferralBetsConclusionJob;
use App\Models\BackOffice\Referral\Referral;
use App\Models\BackOffice\Referral\ReferralSession;
use App\Models\General\Job;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReferralBetsConclusionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:referral-bets-conclusion-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Referral bets conclusion command.';

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

            if (is_null($collection))
                return;

            $userIdCol = ReferralsTableEnum::UserId->dbName();
            $betsConclusionQueuedAtCol = ReferralsTableEnum::BetsConclusionQueuedAt->dbName();

            $queuedAt = now()->toDateTimeString();

            /** @var Referral $referral */
            foreach ($collection as $referral) {

                $referral[$betsConclusionQueuedAtCol] = $queuedAt;
                $referral->save();

                ReferralBetsConclusionJob::dispatch($referral[$userIdCol]);
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
        $betsConclusionQueuedAtCol = ReferralsTableEnum::BetsConclusionQueuedAt->dbName();

        $jobsCount = Job::where(JobsTableEnum::Queue->dbName(), QueueEnum::ReferralBetsConclusion->value)
            ->count();

        // Min wait to unlock even there is no job to do: DelayConstants::ReferralMinWaitForUnlockTimeoutedJobRecords
        $maxWaitPerJob = 3; // Based on second
        $syncWaitExpireTime = min(now()->subSeconds($jobsCount * $maxWaitPerJob), now()->subMinutes(DelayConstants::ReferralMinWaitForUnlockTimeoutedJobRecords));

        $timeoutItems = Referral::whereNotNull($betsConclusionQueuedAtCol)
            ->where($betsConclusionQueuedAtCol, '<', $syncWaitExpireTime)
            ->orderBy($betsConclusionQueuedAtCol, 'asc')
            ->limit(self::RECORDS_SYNC_PER_REQUEST)
            ->get();

        foreach ($timeoutItems as $item) {

            $item[$betsConclusionQueuedAtCol] = null;
            $item->save();
        }
    }

    /**
     * Get collection
     *
     * @param  \App\Enums\Referral\ReferralSessionStatusEnum $referralSessionStatus
     * @param array $finishedSessionIds
     * @return null|\Illuminate\Database\Eloquent\Collection
     */
    private function getCollection(ReferralSessionStatusEnum $referralSessionStatus = ReferralSessionStatusEnum::PayingRewards, array $finishedSessionIds = []): ?Collection
    {
        $referralSessionStatusName = $referralSessionStatus->name;
        $payingRewardsStatusName = ReferralSessionStatusEnum::PayingRewards->name;
        $inProgressStatusName = ReferralSessionStatusEnum::InProgress->name;

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
            $maxCalculatedUntil = now()->subHours(DelayConstants::ReferralBetsConclusionInProgressSession);
            $isPayingRewardSession = false;
        }

        $referralsTabel = DatabaseTablesEnum::Referrals;
        $referralBetsConclusionsTabel = DatabaseTablesEnum::ReferralBetsConclusions;

        $collection = Referral::whereNotNull(ReferralsTableEnum::ReferredBy->dbName())
            ->whereNull(ReferralsTableEnum::BetsConclusionQueuedAt->dbName())
            ->where(TimestampsEnum::CreatedAt->dbName(), '<', $maxCalculatedUntil)

            ->whereNotExists(function ($query) use ($referralsTabel, $referralBetsConclusionsTabel, $referralSessionId, $maxCalculatedUntil, $isPayingRewardSession) {

                // If in in progress session avoid to calculate more than need
                $dateCriteriaColumn = $isPayingRewardSession ? ReferralBetsConclusionsTableEnum::CalculatedUntil->dbNameWithTable($referralBetsConclusionsTabel) : ReferralBetsConclusionsTableEnum::CalculatedAt->dbNameWithTable($referralBetsConclusionsTabel);

                $query->select(DB::raw(1))
                    ->from($referralBetsConclusionsTabel->tableName())
                    ->where(ReferralBetsConclusionsTableEnum::ReferralSessionId->dbNameWithTable($referralBetsConclusionsTabel), $referralSessionId)
                    ->where($dateCriteriaColumn, '>=', $maxCalculatedUntil)
                    ->whereColumn(ReferralBetsConclusionsTableEnum::ReferredId->dbNameWithTable($referralBetsConclusionsTabel), ReferralsTableEnum::UserId->dbNameWithTable($referralsTabel));
            })
            ->orderBy(TimestampsEnum::CreatedAt->dbNameWithTable($referralsTabel))
            ->limit(self::RECORDS_SYNC_PER_REQUEST)
            ->get();

        if ($collection->isEmpty()) {
            // This session is done, so try to do next session conclusion

            array_push($finishedSessionIds, $referralSessionId);
            return $this->getCollection($referralSessionStatus, $finishedSessionIds);
        }

        return $collection;
    }
}
