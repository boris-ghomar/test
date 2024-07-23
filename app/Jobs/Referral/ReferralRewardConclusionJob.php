<?php

namespace App\Jobs\Referral;

use App\Constants\DelayConstants;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\BetsTableEnum;
use App\Enums\Database\Tables\ReferralBetsConclusionsTableEnum;
use App\Enums\Database\Tables\ReferralClaimedRewardsTableEnum;
use App\Enums\General\QueueEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Enums\Database\Tables\ReferralRewardConclusionsTableEnum;
use App\Enums\Database\Tables\ReferralRewardItemsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum;
use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\General\CurrencyEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Jobs\FetchData\Single\FetchClientExtraDataJob;
use App\Models\BackOffice\Referral\Referral;
use App\Models\BackOffice\Referral\ReferralBetsConclusion;
use App\Models\BackOffice\Referral\ReferralClaimedReward;
use App\Models\BackOffice\Referral\ReferralRewardConclusion;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Models\BackOffice\Referral\ReferralRewardPayment;
use App\Models\BackOffice\Referral\ReferralSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ReferralRewardConclusionJob implements ShouldQueue
{
    /**
     * Update the data of a specific bet from the partner.
     */

    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // __construct variables
    private $userId; // user id of referrer client
    private $referralSessionId;

    // Private variables
    private User $user;
    private BetconstructClient $userExtra;
    private CurrencyEnum $userCurrency;
    private ReferralSession $referralSession;
    private ReferralRewardConclusion $referralRewardConclusion;
    private ReferralRewardPackage $referralRewardPackage;
    private string $calculatedUntil;

    private const MAX_WAIT_HOURS_FOR_PAYMENT = 3;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param int $referralSessionId
     */
    public function __construct(int $userId, int $referralSessionId)
    {
        $this->onQueue(QueueEnum::ReferralRewardConclusion->value);

        $this->userId = $userId;
        $this->referralSessionId = $referralSessionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        if (!AppSettingsEnum::IsCommunityActive->getValue()) {

            self::dispatch($this->userId, $this->referralSessionId)->delay(now()->addMinutes(DelayConstants::CommunityIsNotActive));
            return;
        }

        $initRes = $this->init();
        if (is_string($initRes)) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                "Error: " . $initRes,
                "Issue in init function"
            );
            return;
        } else if ($initRes === false)
            return;

        try {

            $referralSessionStatus = $this->referralSession[ReferralSessionsTableEnum::Status->dbName()];

            $minBetCountReferred = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetCountReferred->dbName()];

            // Calculate referral reward conclusion data
            $referralBetsConclusions = ReferralBetsConclusion::where(ReferralBetsConclusionsTableEnum::ReferralSessionId->dbName(), $this->referralSession->id)
                ->where(ReferralBetsConclusionsTableEnum::ReferrerId->dbName(), $this->user->id)
                ->where(ReferralBetsConclusionsTableEnum::BetsCount->dbName(), '>=', $minBetCountReferred);

            $totalEffectiveBetsCount = $referralBetsConclusions->sum(ReferralBetsConclusionsTableEnum::BetsCount->dbName());
            $totalEffectiveBetsAmount = $referralBetsConclusions->sum(ReferralBetsConclusionsTableEnum::BetsTotalAmount->dbName());

            if ($referralSessionStatus == ReferralSessionStatusEnum::PayingRewards->name) {

                if ($this->isUserEligible()) {

                    $isPayable = $totalEffectiveBetsAmount > 0.01;
                    $isDone = !$isPayable;
                } else {
                    $isPayable = false;
                    $isDone = true;
                }
            } else {
                $isPayable = false;
                $isDone = false;
            }

            $this->referralRewardConclusion->forceFill([
                ReferralRewardConclusionsTableEnum::TotalEffectiveBetsCount->dbName() => $totalEffectiveBetsCount,
                ReferralRewardConclusionsTableEnum::TotalEffectiveBetsAmount->dbName() => $totalEffectiveBetsAmount,
                ReferralRewardConclusionsTableEnum::RewardsCount->dbName() => $isDone ? 0 : null,
                ReferralRewardConclusionsTableEnum::IsDone->dbName() => $isDone,
                ReferralRewardConclusionsTableEnum::CalculatedUntil->dbName() => $this->calculatedUntil,
                ReferralRewardConclusionsTableEnum::CalculatedAt->dbName() => now()->toDateTimeString(),
            ]);

            $this->referralRewardConclusion->save();

            // Create referral reward payment records
            if ($isPayable)
                $this->createReferralRewardPaymentRecords();

            /**************** Unlock Queue reward conclusion lock ****************/
            $clientReferral = $this->user->clientReferral;
            $clientReferral[ReferralsTableEnum::RewardConclusionQueuedAt->dbName()] = null;
            $clientReferral->save();
            /**************** Unlock Queue reward conclusion lock END ****************/
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Issue in handel"
            );
        }
    }

    /**
     * Preparation of basic information
     *
     * @return bool|string
     * true: Ready to start | false: Minor Error (Needs other information and then it will be called again) | string: Major Error message (It cannot be processed)
     */
    private function init(): bool|string
    {
        try {

            /**************** Collect user data ****************/

            /** @var User $user */
            $user = User::find($this->userId);

            if (is_null($user)) return sprintf("User not found!\nUser ID: %s", $this->userId);
            if (!$user->isClient()) return sprintf("User is not client!\nUser ID: %s", $this->userId);

            /** @var BetconstructClient $referrerUserExtra*/
            $userExtra = $user->userExtra;
            if (is_null($userExtra)) {

                FetchClientExtraDataJob::dispatchSync($this->userId);
                self::dispatch($this->userId, $this->referralSessionId)->delay(now()->addMinutes(2));
                return false;
            }
            $this->user = $user;
            $this->userExtra = $userExtra;

            $userCurrency = CurrencyEnum::getCase(strtoupper($userExtra[ClientModelEnum::CurrencyId->dbName()]));

            if (is_null($userCurrency))
                return sprintf("User currency not detected!\User ID: %s", $this->userId);

            $this->userCurrency = $userCurrency;
            /**************** Collect user data END ****************/

            /**************** Collect referrral session data ****************/
            if ($referralSession = ReferralSession::find($this->referralSessionId))
                $this->referralSession = $referralSession;
            else
                return sprintf("Referral session not found!\nReferral session ID: %s", $this->referralSessionId);

            $allowedSessionStatus = [
                ReferralSessionStatusEnum::PayingRewards->name,
                ReferralSessionStatusEnum::InProgress->name,
            ];
            $referralSessionStatus = $referralSession[ReferralSessionsTableEnum::Status->dbName()];

            if (!in_array($referralSessionStatus, $allowedSessionStatus))
                return false; // Session status is in-correct or finished, so there is nothing to do

            /**************** Collect referrral session data END ****************/

            /**************** Check referral bets conclusions is done ****************/
            $checkReferralBetsConclusionsRes = $this->checkReferralBetsConclusions();

            if ($checkReferralBetsConclusionsRes !== true)
                return $checkReferralBetsConclusionsRes;
            /**************** Check referral bets conclusions is done END ****************/

            /**************** Collect referrral reward package data ****************/
            $identifyRewardPackageRes = $this->identifyRewardPackage();

            if ($identifyRewardPackageRes !== true)
                return $identifyRewardPackageRes;
            /**************** Collect referrral reward package data END ****************/

            /**************** Make or collect referral reward conclusions data ****************/
            $makeReferralRewardConclusionRecordRes = $this->makeReferralRewardConclusionRecord();

            if ($makeReferralRewardConclusionRecordRes !== true)
                return $makeReferralRewardConclusionRecordRes;

            /**************** Make or collect referral reward conclusions data END ****************/
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        return true;
    }

    /**
     * Identify referral reward package
     *
     * @return bool|string string: error message
     */
    private function identifyRewardPackage(): bool|string
    {
        $rewardPackage = null;

        if ($clientReferralCustomSettings = $this->user->clientReferralCustomSettings)
            $rewardPackage = $clientReferralCustomSettings->referralRewardPackage;

        if (is_null($rewardPackage))
            $rewardPackage = $this->referralSession->referralRewardPackage;

        if (is_null($rewardPackage))
            return sprintf("Referral reward package not found!\nUser ID: %s", $this->userId);

        $this->referralRewardPackage = $rewardPackage;
        return true;
    }

    /**
     * Make referral reward conclusion record
     *
     * @return bool
     */
    private function makeReferralRewardConclusionRecord(): bool
    {
        $userIdCol = ReferralRewardConclusionsTableEnum::UserId->dbName();
        $referralSessionIdCol = ReferralRewardConclusionsTableEnum::ReferralSessionId->dbName();
        $isDoneCol = ReferralRewardConclusionsTableEnum::IsDone->dbName();
        $updatedAtCol = TimestampsEnum::UpdatedAt->dbName();


        // Check if the reward conclusion is done
        $isRewardDone = ReferralRewardConclusion::where($userIdCol, $this->userId)
            ->where($referralSessionIdCol, $this->referralSession->id)
            ->where($isDoneCol, 1)
            ->exists();

        if ($isRewardDone)
            return false; // Reward conclusion has been done and paid, so ther is nothing to do

        // Check if the client has an uncompleted reward conclusion
        $referralRewardConclusion = ReferralRewardConclusion::where($userIdCol, $this->userId)
            ->where($referralSessionIdCol, $this->referralSession->id)
            ->where($isDoneCol, 0)
            ->first();

        if (is_null($referralRewardConclusion)) {

            $referralRewardConclusion = new ReferralRewardConclusion();

            $referralRewardConclusion->forceFill([

                $referralSessionIdCol => $this->referralSession->id,
                $userIdCol => $this->userId,
                $isDoneCol => 0,
            ]);

            $referralRewardConclusion->save();

            $this->referralRewardConclusion = $referralRewardConclusion;
            return true;
        } else {

            if (Carbon::parse($referralRewardConclusion[$updatedAtCol]) < now()->subHours(self::MAX_WAIT_HOURS_FOR_PAYMENT)) {
                // The payment processing time has expired and needs to be rechecked.

                $referralRewardConclusion[$updatedAtCol] = now()->toDateTimeString();
                $referralRewardConclusion->save();

                $this->referralRewardConclusion = $referralRewardConclusion;
                return true;
            } else
                return false; // Payment processing has not yet expired and may be processing.
        }
    }

    /**
     * Check if the bets conclusion of all referred clients
     * that belongs to referrer client has been done.
     *
     * @return bool
     */
    private function checkReferralBetsConclusions(): bool
    {
        $referralSessionFinishedAt = $this->referralSession->getRawOriginal(ReferralSessionsTableEnum::FinishedAt->dbName());

        if ($referralSessionFinishedAt < now())
            $calculatedUntil = $referralSessionFinishedAt;
        else {
            // Calculation is in in-progress status and delay is good to avoid more resource usage
            $inprogressCalculatedUntil = now()->subHours(DelayConstants::ReferralRewardConclusionInProgressSession)->toDateTimeString();
            $referralSessionStartedAt = Carbon::parse($this->referralSession->getRawOriginal(ReferralSessionsTableEnum::StartedAt->dbName()));

            $calculatedUntil = max($inprogressCalculatedUntil, $referralSessionStartedAt);
        }

        $this->calculatedUntil = $calculatedUntil;

        // Get items that needs to update bets conclusion
        $referralsTabel = DatabaseTablesEnum::Referrals;
        $referralBetsConclusionsTabel = DatabaseTablesEnum::ReferralBetsConclusions;

        $updateRequiredBetsConclusions = Referral::where(ReferralsTableEnum::ReferredBy->dbName(), $this->userId)
            ->where(TimestampsEnum::CreatedAt->dbName(), '<=', $calculatedUntil)

            ->whereNotExists(function ($query) use ($referralsTabel, $referralBetsConclusionsTabel, $calculatedUntil) {

                $query->select(DB::raw(1))
                    ->from($referralBetsConclusionsTabel->tableName())
                    ->where(ReferralBetsConclusionsTableEnum::ReferralSessionId->dbNameWithTable($referralBetsConclusionsTabel), $this->referralSessionId)
                    ->where(ReferralBetsConclusionsTableEnum::CalculatedUntil->dbNameWithTable($referralBetsConclusionsTabel), '>=', $calculatedUntil)
                    ->whereColumn(ReferralBetsConclusionsTableEnum::ReferredId->dbNameWithTable($referralBetsConclusionsTabel), ReferralsTableEnum::UserId->dbNameWithTable($referralsTabel));
            })
            ->orderBy(TimestampsEnum::CreatedAt->dbNameWithTable($referralsTabel))
            ->limit(50)
            ->get();

        if ($updateRequiredBetsConclusions->isEmpty())
            return true; // The bets conclusion of all referred clients that belongs to referrer client has been done

        $userIdCol = ReferralsTableEnum::UserId->dbName();
        $betsConclusionQueuedAtCol = ReferralsTableEnum::BetsConclusionQueuedAt->dbName();

        /** @var Referral $clientReferral */
        foreach ($updateRequiredBetsConclusions as $clientReferral) {

            if (is_null($clientReferral[$betsConclusionQueuedAtCol])) {

                $clientReferral[$betsConclusionQueuedAtCol] = now()->toDateTimeString();
                $clientReferral->save();

                ReferralBetsConclusionJob::dispatch($clientReferral[$userIdCol]);
            }
        }

        $referrerReferral = $this->user->clientReferral;

        if(is_null($referrerReferral[ReferralsTableEnum::RewardConclusionQueuedAt->dbName()])){
            // Conclusion rescheduling

            $newQueueTime = now()->addHours(DelayConstants::ReferralWaitForBetsFetch + 1);
            $referrerReferral[ReferralsTableEnum::RewardConclusionQueuedAt->dbName()] = $newQueueTime->toDateTimeString();
            $referrerReferral->save();
            self::dispatch($this->userId, $this->referralSessionId)->delay($newQueueTime);
        }

        return false;
    }

    /**
     * Check if the user is eligible for the receive reward or not
     *
     * @return bool
     */
    private function isUserEligible(): bool
    {
        $startedAtCol = ReferralSessionsTableEnum::StartedAt->dbName();
        $finishedAtCol = ReferralSessionsTableEnum::FinishedAt->dbName();

        $minBetAmount = $this->getMinBetAmount();
        $minBetOdds = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetOddsReferrer->dbName()];
        $minBetCount = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetCountReferrer->dbName()];

        $beginDate = $this->referralSession->getRawOriginal($startedAtCol);
        $endDate = $this->referralSession->getRawOriginal($finishedAtCol);

        $bets = $this->user->clientBets()
            ->where(BetsTableEnum::IsReferralBet->dbName(), 1)
            ->where(BetsTableEnum::Odds->dbName(), '>=', $minBetOdds)
            ->where(BetsTableEnum::Amount->dbName(), '>=', $minBetAmount)
            ->where(BetsTableEnum::CalculatedAt->dbName(), '>=', $beginDate)
            ->where(BetsTableEnum::CalculatedAt->dbName(), '<=', $endDate);

        return $bets->count() >= $minBetCount ? true : false;
    }

    /**
     * Get min bet amount
     *
     * @return float
     */
    private function getMinBetAmount(): float
    {
        $minBetAmountIrr = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetAmountIrrReferrer->dbName()];
        $minBetAmountUsd = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetAmountUsdReferrer->dbName()];
        $clientCurrency = $this->userCurrency;

        return match ($clientCurrency) {

            CurrencyEnum::IRR, CurrencyEnum::TOM, CurrencyEnum::IRT
            => CurrencyEnum::IRR->exchange($minBetAmountIrr, $clientCurrency),

            CurrencyEnum::USD => $minBetAmountUsd,

            default => 0
        };
    }

    /**
     * Make referral reward payment record
     *
     * @return void
     */
    private function createReferralRewardPaymentRecords(): void
    {
        $referralSessionStatus = $this->referralSession[ReferralSessionsTableEnum::Status->dbName()];

        $isPayingReward = $referralSessionStatus == ReferralSessionStatusEnum::PayingRewards->name;

        if (!$isPayingReward)
            return;


        $totalEffectiveBetsAmount = $this->referralRewardConclusion[ReferralRewardConclusionsTableEnum::TotalEffectiveBetsAmount->dbName()];

        if ($totalEffectiveBetsAmount <= 0.01) {

            $this->rewardConclusionDone();
            return;
        }

        $packageClaimableCount = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::ClaimCount->dbName()];

        $claimedRewardsIds = $this->getClaimedRewardsIds();

        $claimedRewardsCount = count($claimedRewardsIds);

        $limit = $claimedRewardsCount < $packageClaimableCount ? $packageClaimableCount - $claimedRewardsCount : 0;

        if ($limit > 0) {

            $restReferralRewardItems = $this->referralRewardPackage->referralRewardItemsActive()
                ->whereNotIn(ReferralRewardItemsTableEnum::Id->dbName(), $claimedRewardsIds)
                ->orderBy(ReferralRewardItemsTableEnum::PaymentPriority->dbName(), 'asc')
                ->limit($limit)
                ->get();

            foreach ($restReferralRewardItems as $restItem)
                array_push($claimedRewardsIds, $restItem->id);
        }

        if (count($claimedRewardsIds) > 0) {

            $rewardItems = $this->referralRewardPackage->referralRewardItemsActive()
                ->whereIn(ReferralRewardItemsTableEnum::Id->dbName(), $claimedRewardsIds)
                ->orderBy(ReferralRewardItemsTableEnum::PaymentPriority->dbName(), 'asc')
                ->limit($packageClaimableCount)
                ->get();

            if ($rewardItems->isEmpty()) {

                $this->rewardConclusionDone(); // There are no rewards to pay
                return;
            }

            $this->referralRewardConclusion[ReferralRewardConclusionsTableEnum::RewardsCount->dbName()] = $rewardItems->count();
            $this->referralRewardConclusion->save();

            $userIdCol = ReferralRewardPaymentsTableEnum::UserId->dbName();
            $rewardConclusionsIdCol = ReferralRewardPaymentsTableEnum::RewardConclusionsId->dbName();
            $rewardItemIdCol = ReferralRewardPaymentsTableEnum::RewardItemId->dbName();
            $amountCol = ReferralRewardPaymentsTableEnum::Amount->dbName();
            $queuedAtCol = ReferralRewardPaymentsTableEnum::QueuedAt->dbName();

            $rewardPercentageCol = ReferralRewardItemsTableEnum::Percentage->dbName();

            foreach ($rewardItems as $rewardItem) {

                $referralRewardConclusionId = $this->referralRewardConclusion->id;
                $rewardItemId = $rewardItem->id;

                // Check for duplicate payment
                $isReferralRewardPaymentExists = ReferralRewardPayment::where($userIdCol, $this->userId)
                    ->where($rewardConclusionsIdCol, $referralRewardConclusionId)
                    ->where($rewardItemIdCol, $rewardItemId)
                    ->exists();

                if (!$isReferralRewardPaymentExists) {

                    $percentage = $rewardItem[$rewardPercentageCol] / 100;
                    $amount = round($percentage * $totalEffectiveBetsAmount, 2);

                    if ($amount > 0.01) {

                        $referralRewardPayment = new ReferralRewardPayment();

                        $referralRewardPayment->forceFill([
                            $userIdCol => $this->userId,
                            $rewardConclusionsIdCol => $referralRewardConclusionId,
                            $rewardItemIdCol => $rewardItemId,
                            $amountCol => $amount,
                            $queuedAtCol => now()->toDateTimeString(),
                        ]);

                        $referralRewardPayment->save();

                        ReferralRewardPaymentJob::dispatch($referralRewardPayment->id);
                    }
                }
            }
        } else {
            $this->rewardConclusionDone(); // There are no rewards to pay
        }
    }

    /**
     * Get IDs list of claimed rewards by client
     *
     * @return array
     */
    private function getClaimedRewardsIds(): array
    {
        $rewardItemIdCol =  ReferralClaimedRewardsTableEnum::RewardItemId->dbName();

        $claimedRewards = ReferralClaimedReward::where(ReferralClaimedRewardsTableEnum::UserId->dbName(), $this->userId)
            ->where(ReferralClaimedRewardsTableEnum::ReferralSessionId->dbName(), $this->referralSessionId)
            ->get();


        $referralRewardPackageId = $this->referralRewardPackage->id;
        $list = [];

        /** @var ReferralClaimedReward $claimedReward */
        foreach ($claimedRewards as $claimedReward) {

            if ($claimedreferralRewardItem = $claimedReward->referralRewardItem) {

                if ($claimedreferralRewardItem[ReferralRewardItemsTableEnum::IsActive->dbName()]) {

                    $claimedReferralRewardPackage = $claimedreferralRewardItem->referralRewardPackage;

                    if ($claimedReferralRewardPackage->id == $referralRewardPackageId)
                        array_push($list, $claimedReward[$rewardItemIdCol]);
                    else
                        $claimedReward->delete(); // This reward is not belongs to eligible reward package
                } else
                    $claimedReward->delete(); // This reward is not active
            }
        }

        return $list;
    }

    /**
     * Reward conclusion done
     * If no reward has been made to the client, we can close the conclusion here.
     *
     * @return void
     */
    private function rewardConclusionDone(): void
    {
        if ($this->referralSession[ReferralSessionsTableEnum::Status->dbName()] == ReferralSessionStatusEnum::PayingRewards->name) {

            $this->referralRewardConclusion[ReferralRewardConclusionsTableEnum::RewardsCount->dbName()] = 0;
            $this->referralRewardConclusion[ReferralRewardConclusionsTableEnum::IsDone->dbName()] = 1;
            $this->referralRewardConclusion->save();
        }
    }
}
