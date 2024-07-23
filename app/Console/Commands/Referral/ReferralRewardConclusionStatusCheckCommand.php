<?php

namespace App\Console\Commands\Referral;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralRewardConclusionsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum;
use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Jobs\Referral\ReferralRewardConclusionJob;
use App\Models\BackOffice\Referral\ReferralRewardConclusion;
use App\Models\BackOffice\Referral\ReferralSession;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReferralRewardConclusionStatusCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:referral-reward-conclusion-status-check-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Referral reward conclusion status check command';

    private const RECORDS_SYNC_PER_REQUEST = 100;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (AppSettingsEnum::IsCommunityActive->getValue()) {

            $collection = $this->getCollection();

            if (!is_null($collection)) {

                $isDoneCol = ReferralRewardConclusionsTableEnum::IsDone->dbName();

                /** @var ReferralRewardConclusion $referralRewardConclusion */
                foreach ($collection as $referralRewardConclusion) {

                    $rewardsCount = $referralRewardConclusion[ReferralRewardConclusionsTableEnum::RewardsCount->dbName()];
                    $referralRewardPaymentsCount = $referralRewardConclusion->referralRewardPayments()->count();

                    if ($referralRewardPaymentsCount == $rewardsCount) {

                        $referralRewardConclusion[$isDoneCol] = 1;
                        $referralRewardConclusion->save();
                    } else if ($referralRewardPaymentsCount > $rewardsCount) {

                        // Delete additional payments
                        $referralRewardConclusion->referralRewardPayments()
                            ->where(ReferralRewardPaymentsTableEnum::IsDone->dbName(), 0)
                            ->orderby(ReferralRewardPaymentsTableEnum::Id->dbName(), 'desc')
                            ->limit($referralRewardPaymentsCount - $rewardsCount)
                            ->delete();

                        LogCreator::createLogCritical(
                            __CLASS__,
                            __FUNCTION__,
                            sprintf(
                                "Referral Reward Conclusion ID: %s\nNumber of rewards allocated: %s\nNumber of payments found: %s\nNotice: Unpaid additional payments were removed.",
                                $referralRewardConclusion->id,
                                $rewardsCount,
                                $referralRewardPaymentsCount,
                            ),
                            "Additional referral payments detected!"
                        );
                    } else if ($referralRewardPaymentsCount < $rewardsCount) {

                        $clientReferral = $referralRewardConclusion->user->clientReferral;

                        if (is_null($clientReferral[ReferralsTableEnum::RewardConclusionQueuedAt->dbName()])) {

                            $clientReferral[ReferralsTableEnum::RewardConclusionQueuedAt->dbName()] = now()->toDateTimeString();
                            $clientReferral->save();

                            ReferralRewardConclusionJob::dispatch($referralRewardConclusion[ReferralRewardConclusionsTableEnum::UserId->dbName()], $referralRewardConclusion[ReferralRewardConclusionsTableEnum::ReferralSessionId->dbName()]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get collection
     *
     * @return null|\Illuminate\Database\Eloquent\Collection
     */
    private function getCollection(): ?Collection
    {
        $payingRewardSessionIds = ReferralSession::where(ReferralSessionsTableEnum::Status->dbName(), ReferralSessionStatusEnum::PayingRewards->name)
            ->pluck(ReferralSessionsTableEnum::Id->dbName())
            ->toArray();

        if (empty($payingRewardSessionIds))
            return null;

        $collection = ReferralRewardConclusion::whereIn(ReferralRewardConclusionsTableEnum::ReferralSessionId->dbName(), $payingRewardSessionIds)
            ->whereNotNull(ReferralRewardConclusionsTableEnum::RewardsCount->dbName())
            ->where(ReferralRewardConclusionsTableEnum::IsDone->dbName(), 0)

            ->whereNotExists(function ($query) {

                $referralRewardConclusionsTable = DatabaseTablesEnum::ReferralRewardConclusions;
                $referralRewardPaymentsTable = DatabaseTablesEnum::ReferralRewardPayments;

                $query->select(DB::raw(1))
                    ->from($referralRewardPaymentsTable->tableName())
                    ->where(ReferralRewardPaymentsTableEnum::IsDone->dbNameWithTable($referralRewardPaymentsTable), 0)
                    ->whereColumn(ReferralRewardPaymentsTableEnum::RewardConclusionsId->dbNameWithTable($referralRewardPaymentsTable), ReferralRewardConclusionsTableEnum::Id->dbNameWithTable($referralRewardConclusionsTable));
            })
            ->orderBy(ReferralRewardConclusionsTableEnum::CalculatedAt->dbName(), 'asc')
            ->limit(self::RECORDS_SYNC_PER_REQUEST)
            ->get();

        return $collection;
    }

    private function getCollection2(): ?Collection
    {
        $payingRewardSessionIds = ReferralSession::where(ReferralSessionsTableEnum::Status->dbName(), ReferralSessionStatusEnum::PayingRewards->name)
            ->pluck(ReferralSessionsTableEnum::Id->dbName())
            ->toArray();

        if (empty($payingRewardSessionIds))
            return null;

        $collection = ReferralRewardConclusion::whereIn(ReferralRewardConclusionsTableEnum::ReferralSessionId->dbName(), $payingRewardSessionIds)
            ->where(ReferralRewardConclusionsTableEnum::IsDone->dbName(), 0)

            ->whereNotExists(function ($query) {

                $referralRewardConclusionsTable = DatabaseTablesEnum::ReferralRewardConclusions;
                $referralRewardPaymentsTable = DatabaseTablesEnum::ReferralRewardPayments;

                $query->select(DB::raw(1))
                    ->from($referralRewardPaymentsTable->tableName())
                    ->where(ReferralRewardPaymentsTableEnum::IsDone->dbNameWithTable($referralRewardPaymentsTable), 0)
                    ->whereColumn(ReferralRewardPaymentsTableEnum::RewardConclusionsId->dbNameWithTable($referralRewardPaymentsTable), ReferralRewardConclusionsTableEnum::Id->dbNameWithTable($referralRewardConclusionsTable));
            })
            ->orderBy(ReferralRewardConclusionsTableEnum::CalculatedAt->dbName(), 'asc')
            ->limit(self::RECORDS_SYNC_PER_REQUEST)
            ->get();

        return $collection;
    }
}
