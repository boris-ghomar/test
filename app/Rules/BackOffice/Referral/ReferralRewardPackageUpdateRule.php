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

class ReferralRewardPackageUpdateRule implements ValidationRule
{
    use AddAttributesPad;


    public function __construct(private ?ReferralRewardPackage $referralRewardPackage)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $referralRewardPackage = $this->referralRewardPackage;

        if (!is_null($referralRewardPackage)) {

            if ($referralRewardPackage[$attribute] != $value) {
                // The attribute is dirty

                $referralRewardPackageId = $referralRewardPackage->id;

                // Check if the package is used in a running "ReferralSession"
                $notAllowedStatusList = [
                    ReferralSessionStatusEnum::InProgress->name,
                    ReferralSessionStatusEnum::PayingRewards->name,
                ];

                $runningReferralSession = ReferralSession::where(ReferralSessionsTableEnum::PackageId->dbName(), $referralRewardPackageId)
                    ->whereIn(ReferralSessionsTableEnum::Status->dbName(), $notAllowedStatusList)
                    ->first();

                if (!is_null($runningReferralSession))
                    $fail('thisApp.Errors.Referral.ReferralRewardPackageUpdateRule.UsedInReferralSession')->translate(
                        $this->addPadToArrayVal([
                            'packageName' => $referralRewardPackage[ReferralRewardPackagesTableEnum::Name->dbName()],
                            'sessionName' => $runningReferralSession[ReferralSessionsTableEnum::Name->dbName()],
                        ])
                    );

                // Check if the package is used in a "ReferralCustomSetting"
                $isUsedInReferralCustomSetting = ReferralCustomSetting::where(ReferralCustomSettingsTableEnum::PackageId->dbName(), $referralRewardPackageId)
                    ->exists();

                if ($isUsedInReferralCustomSetting)
                    $fail('thisApp.Errors.Referral.ReferralRewardPackageUpdateRule.UsedInReferralCustomSetting')->translate(
                        $this->addPadToArrayVal([
                            'packageName' => $referralRewardPackage[ReferralRewardPackagesTableEnum::Name->dbName()],
                        ])
                    );
            }
        }
    }
}
