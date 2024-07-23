<?php

namespace App\Rules\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralRewardItemsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\Models\BackOffice\Referral\ReferralRewardItem;
use App\Models\BackOffice\Referral\ReferralRewardPayment;
use App\Models\BackOffice\Referral\ReferralSession;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReferralRewardItemUpdateRule implements ValidationRule
{
    use AddAttributesPad;

    public function __construct(private ?ReferralRewardItem $referralRewardItem)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $referralRewardItem = $this->referralRewardItem;
        if (is_null($referralRewardItem))
            return;

        $referralRewardPackage = $referralRewardItem->referralRewardPackage;

        if (!is_null($referralRewardPackage)) {

            if ($referralRewardPackage[$attribute] != $value) {
                // The attribute is dirty

                $referralRewardPackageId = $referralRewardPackage->id;
                $referralRewardItemId = $referralRewardItem->id;

                // Check if the package is used in a "ReferralSession" with payingReward status
                $notAllowedStatusList = [
                    ReferralSessionStatusEnum::InProgress->name,
                    ReferralSessionStatusEnum::PayingRewards->name,
                ];

                $runningReferralSession = ReferralSession::where(ReferralSessionsTableEnum::PackageId->dbName(), $referralRewardPackageId)
                    ->whereIn(ReferralSessionsTableEnum::Status->dbName(), $notAllowedStatusList)
                    ->first();

                if (!is_null($runningReferralSession))
                    $fail('thisApp.Errors.Referral.ReferralRewardItemUpdateRule.UsedInReferralSession')->translate(
                        $this->addPadToArrayVal([
                            'itemName' => $referralRewardItem[ReferralRewardItemsTableEnum::Name->dbName()],
                            'sessionName' => $runningReferralSession[ReferralSessionsTableEnum::Name->dbName()],
                        ])
                    );

                // Check if the reward item is used in an incomplete "ReferralRewardPayments"
                $isUsedInReferralPayment = ReferralRewardPayment::where(ReferralRewardPaymentsTableEnum::RewardItemId->dbName(), $referralRewardItemId)
                    ->where(ReferralRewardPaymentsTableEnum::IsDone->dbName(), 0)
                    ->exists();

                if ($isUsedInReferralPayment)
                    $fail('thisApp.Errors.Referral.ReferralRewardItemUpdateRule.UsedInRewardPayment')->translate(
                        $this->addPadToArrayVal([
                            'itemName' => $referralRewardItem[ReferralRewardItemsTableEnum::Name->dbName()],
                        ])
                    );
            }
        }
    }
}
