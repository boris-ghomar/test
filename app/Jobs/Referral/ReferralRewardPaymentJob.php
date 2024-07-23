<?php

namespace App\Jobs\Referral;

use App\Constants\DelayConstants;
use App\Enums\Database\Tables\ReferralRewardConclusionsTableEnum;
use App\Enums\General\QueueEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\LogCreator;
use App\Enums\Database\Tables\ReferralRewardItemsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum;
use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\General\CurrencyEnum;
use App\Enums\Referral\ReferralRewardTypeEnum;
use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance\BalanceCorrectionTypeEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ErrorEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\ExternalAdminAPI;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Jobs\FetchData\Single\FetchClientExtraDataJob;
use App\Models\BackOffice\Referral\ReferralRewardItem;
use App\Models\BackOffice\Referral\ReferralRewardPayment;
use App\Models\User;
use App\Notifications\Site\Referral\ReferralRewardPaymentDoneNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReferralRewardPaymentJob implements ShouldQueue
{
    /**
     * Update the data of a specific bet from the partner.
     */

    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // __construct variables
    private $referralRewardPaymentId;

    // Private variables
    private ReferralRewardPayment $referralRewardPayment;
    private User $user;
    private BetconstructClient $userExtra;
    private CurrencyEnum $currency;
    private ReferralRewardItem $referralRewardItem;

    /**
     * Create a new job instance.
     *
     * @param int $referralRewardPaymentId
     */
    public function __construct(int $referralRewardPaymentId)
    {
        $this->onQueue(QueueEnum::ReferralRewardPayment->value);

        $this->referralRewardPaymentId = $referralRewardPaymentId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        if (!AppSettingsEnum::IsCommunityActive->getValue()) {

            self::dispatch($this->referralRewardPaymentId)->delay(now()->addMinutes(DelayConstants::CommunityIsNotActive));
            return;
        }

        $initRes = $this->init();
        if (is_string($initRes)) {

            $this->setPaymentStatus(false, $initRes);

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

            $payRewardResult = $this->payReward();

            if (is_string($payRewardResult)) {

                $this->setPaymentStatus(false, $payRewardResult);

                LogCreator::createLogError(
                    __CLASS__,
                    __FUNCTION__,
                    "Error: " . $payRewardResult,
                    "Issue in pay reward function"
                );
                return;
            } else if ($payRewardResult === false)
                return; // Do not change payment status, It may be scheduled for another time
            else if ($payRewardResult === true) {

                $this->setPaymentStatus(true);
                $this->user->notify(new ReferralRewardPaymentDoneNotification($this->referralRewardPaymentId));
                return;
            }
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

            /**************** Collect referral reward payment data ****************/

            /** @var ReferralRewardPayment $referralRewardPayment */
            $referralRewardPayment = ReferralRewardPayment::find($this->referralRewardPaymentId);

            if (is_null($referralRewardPayment)) return sprintf("Referral reward payment not found!\nReferral reward payment ID: %s", $this->referralRewardPaymentId);

            if ($referralRewardPayment[ReferralRewardPaymentsTableEnum::IsDone->dbName()])
                return false; // Payment already has been done

            $this->referralRewardPayment = $referralRewardPayment;
            /**************** Collect referral reward payment data END ****************/

            /**************** Collect user data ****************/
            /** @var User $user */
            $user = $this->referralRewardPayment->user;
            if (is_null($user)) return sprintf("User not found!\nreferralRewardPaymentId: %s", $this->referralRewardPaymentId);

            $userId = $user->id;
            if (!$user->isClient()) return sprintf("User is not client!\nUser ID: %s", $userId);

            /** @var BetconstructClient $userExtra*/
            $userExtra = $user->userExtra;
            if (is_null($userExtra)) {

                FetchClientExtraDataJob::dispatchSync($userId);
                self::dispatch($this->referralRewardPaymentId)->delay(now()->addMinutes(2));
                return false;
            }
            $this->user = $user;
            $this->userExtra = $userExtra;
            /**************** Collect user data END ****************/

            /**************** Collect currency data ****************/
            $currencyName = $userExtra[ClientModelEnum::CurrencyId->dbName()];
            $currency = CurrencyEnum::getCase(strtoupper($currencyName));

            if (is_null($currency))
                return sprintf("User currency not detected!\nUser ID: %s\nCurrency: %s", $userId, $currencyName);

            $this->currency = $currency;
            /**************** Collect currency data END ****************/

            /**************** Collect referral reward item data ****************/
            $referralRewardItem = $this->referralRewardPayment->referralRewardItem;

            if (is_null($referralRewardItem))
                return sprintf("Referral reward item not found!\nReferral Reward Payment ID: %s", $this->referralRewardPaymentId);

            $this->referralRewardItem = $referralRewardItem;
            /**************** Collect referral reward item data END ****************/

            /**************** Check referral reward items count  ****************/
            $referralRewardConclusion = $this->referralRewardPayment->referralRewardConclusion;

            if (is_null($referralRewardConclusion))
                return sprintf("Referral reward conclusion not found!\nReferral Reward Payment ID: %s", $this->referralRewardPaymentId);

            if (is_null($referralRewardConclusion[ReferralRewardConclusionsTableEnum::RewardsCount->dbName()])) {
                // The rewards count is not identify

                $clientReferral = $this->user->clientReferral;

                if (is_null($clientReferral[ReferralsTableEnum::RewardConclusionQueuedAt->dbName()])) {

                    $clientReferral[ReferralsTableEnum::RewardConclusionQueuedAt->dbName()] = now()->toDateTimeString();
                    $clientReferral->save();

                    ReferralRewardConclusionJob::dispatch($referralRewardConclusion[ReferralRewardConclusionsTableEnum::UserId->dbName()], $referralRewardConclusion[ReferralRewardConclusionsTableEnum::ReferralSessionId->dbName()]);
                }

                self::dispatch($this->referralRewardPaymentId)->delay(now()->addHour());

                return false;
            }
            /**************** Check referral reward items count END ****************/
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        return true;
    }

    /**
     * Set payment status
     *
     * @param bool $isSuccessful
     * @param string|null $systemMessage
     * @return void
     */
    private function setPaymentStatus(bool $isSuccessful, ?string $systemMessage = null)
    {
        $this->referralRewardPayment[ReferralRewardPaymentsTableEnum::IsSuccessful->dbName()] = $isSuccessful;
        $this->referralRewardPayment[ReferralRewardPaymentsTableEnum::IsDone->dbName()] = 1;
        $this->referralRewardPayment[ReferralRewardPaymentsTableEnum::QueuedAt->dbName()] = null;

        if (!empty($systemMessage))
            $this->referralRewardPayment[ReferralRewardPaymentsTableEnum::SystemMessage->dbName()] = $systemMessage;

        $this->referralRewardPayment->save();

        $this->checkRewardConclusionStatus();
    }

    /**
     * Pay reward
     *
     * @return bool|string string: error message
     */
    private function payReward(): bool|string
    {
        $referralRewardItem = $this->referralRewardItem;
        $referralRewardPayment = $this->referralRewardPayment;

        $rewardType = $referralRewardItem[ReferralRewardItemsTableEnum::Type->dbName()];

        $amount = round($referralRewardPayment[ReferralRewardPaymentsTableEnum::Amount->dbName()], 2);

        if ($amount < 0.01) {
            // There is nothing to do
            $this->setPaymentStatus(true);
            return false;
        }

        if ($rewardType == ReferralRewardTypeEnum::CashBack->name) {

            return $this->payCashbakReward($amount);
        } else if ($rewardType == ReferralRewardTypeEnum::Bonus->name) {

            return $this->payBonusReward($amount);
        } else {
            return sprintf("Referral reward payment type not found!\nReferral reward payment ID: %s", $this->referralRewardPaymentId);
        }

        return true;
    }

    /**
     * Pay cashbak reward
     *
     * @param float $amount
     * @return bool
     */
    private function payCashbakReward(float $amount): bool|string
    {
        $userExtra = $this->userExtra;

        // $apiResponse = ApiResponseTest::clientBalanceCorrection(TestResponseEnum::UpdateClient_Failed_DuplicateIBAN);

        $apiResponse = ExternalAdminAPI::clientBalanceCorrection(

            $userExtra[ClientModelEnum::Id->dbName()],
            $this->currency,
            $amount,
            BalanceCorrectionTypeEnum::CorrectionUp,
            sprintf('Referral program reward from "%s"', config('app.name'))
        );

        if ($apiResponse->getStatus()->name === ApiStatusEnum::Success->name) {

            return true;
        } else {
            return sprintf('Partner Error: %s', json_encode($apiResponse->getResponseData()));
        }
    }

    /**
     * Pay bonus reward
     *
     * @param float $amount
     * @return bool
     */
    private function payBonusReward(float $amount): bool|string
    {
        $userExtra = $this->userExtra;

        $partnerBonusId = $this->referralRewardItem[ReferralRewardItemsTableEnum::BonusId->dbName()];

        if (empty($partnerBonusId))
            return sprintf("Bonus ID is empty!\nReferral Reward Item ID: %s", $this->referralRewardItem->id);

        // $apiResponse = ApiResponseTest::clientBalanceCorrection(TestResponseEnum::UpdateClient_Failed_DuplicateIBAN);
        $apiResponse = ExternalAdminAPI::addClientToBonus(

            $userExtra[ClientModelEnum::Id->dbName()],
            $partnerBonusId,
            $amount,
            true
        );

        if ($apiResponse->getStatus()->name === ApiStatusEnum::Success->name) {

            return true;
        } else {

            $errorResponceCode = $apiResponse->getResponseCode();

            if ($errorResponceCode == ErrorEnum::AmountRangeDoesNotValidRange->name) {
                // Needs change bonus settings on Btconstrcut back office

                return $apiResponse->getErrorMessage();
            } else {

                return sprintf('Partner Error: %s', json_encode($apiResponse->getResponseData()));
            }
        }
    }

    /**
     * Check if reward conclusion is done
     *
     * @return void
     */
    private function checkRewardConclusionStatus(): void
    {
        $referralRewardConclusion = $this->referralRewardPayment->referralRewardConclusion;

        $rewardsCount = $referralRewardConclusion[ReferralRewardConclusionsTableEnum::RewardsCount->dbName()];
        $referralRewardPaymentsCount = $referralRewardConclusion->referralRewardPayments()->count();

        if ($referralRewardPaymentsCount == $rewardsCount) {

            $isThereOutstandingPayments = $referralRewardConclusion->referralRewardPayments()
                ->where(ReferralRewardPaymentsTableEnum::IsDone->dbName(), 0)
                ->exists();

            if (!$isThereOutstandingPayments) {

                $referralRewardConclusion[ReferralRewardConclusionsTableEnum::IsDone->dbName()] = 1;
                $referralRewardConclusion->save();
            }
        } else if ($referralRewardPaymentsCount > $rewardsCount) {

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

            $additionalPayments = $referralRewardConclusion->referralRewardPayments()
                ->where(ReferralRewardPaymentsTableEnum::IsDone->dbName(), 0)
                ->orderby(ReferralRewardPaymentsTableEnum::Id->dbName(), 'desc')
                ->limit($referralRewardPaymentsCount - $rewardsCount);

            if ($additionalPayments->count() > 0) {

                $additionalPayments->delete();
                $this->checkRewardConclusionStatus();
            }
        }
    }
}
