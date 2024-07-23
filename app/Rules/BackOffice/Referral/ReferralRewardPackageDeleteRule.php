<?php

namespace App\Rules\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralCustomSettingsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\Models\BackOffice\Referral\ReferralCustomSetting;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Models\BackOffice\Referral\ReferralSession;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReferralRewardPackageDeleteRule implements ValidationRule
{
    use AddAttributesPad;


    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $referralRewardPackage = ReferralRewardPackage::find($value);

        if (!is_null($referralRewardPackage)) {

            $referralRewardPackageId = $referralRewardPackage->id;

            // Check if the package is used in a "ReferralSession other than the "Upcoming" sessions
            $notAllowedStatusList = [
                ReferralSessionStatusEnum::InProgress->name,
                ReferralSessionStatusEnum::PayingRewards->name,
            ];

            $runningReferralSession = ReferralSession::where(ReferralSessionsTableEnum::PackageId->dbName(), $referralRewardPackageId)
                ->whereIn(ReferralSessionsTableEnum::Status->dbName(), $notAllowedStatusList)
                ->first();

            if (!is_null($runningReferralSession))
                $fail('thisApp.Errors.Referral.ReferralRewardPackageDeleteRule.UsedInReferralSession')->translate(
                    $this->addPadToArrayVal([
                        'packageName' => $referralRewardPackage[ReferralRewardPackagesTableEnum::Name->dbName()],
                        'sessionName' => $runningReferralSession[ReferralSessionsTableEnum::Name->dbName()],
                    ])
                );

            // Check if the package is used in a "ReferralCustomSetting"
            $isUsedInReferralCustomSetting = ReferralCustomSetting::where(ReferralCustomSettingsTableEnum::PackageId->dbName(), $referralRewardPackageId)
                ->exists();

            if ($isUsedInReferralCustomSetting)
                $fail('thisApp.Errors.Referral.ReferralRewardPackageDeleteRule.UsedInReferralCustomSetting')->translate(
                    $this->addPadToArrayVal([
                        'packageName' => $referralRewardPackage[ReferralRewardPackagesTableEnum::Name->dbName()],
                    ])
                );
        }
    }
}
