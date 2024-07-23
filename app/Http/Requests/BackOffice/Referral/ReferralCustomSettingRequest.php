<?php

namespace App\Http\Requests\BackOffice\Referral;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralCustomSettingsTableEnum as TableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Referral\ReferralCustomSetting as model;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Rules\General\Database\ExistsInModel;
use App\Rules\General\Database\ExistsItem;
use Illuminate\Validation\Rule;

class ReferralCustomSettingRequest extends SuperRequest
{

    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->defaultAuthorize(model::class);
    }

    /******************** Action rules *********************/

    /**
     * Rules for store a newly created resource in storage.
     *
     * @return array
     */
    public function rulesStore(): array
    {
        return [

            TableEnum::UserId->dbName() => [
                'required',
                new ExistsInModel(BetconstructClient::class, ClientModelEnum::UserId->dbName()),
                Rule::unique(DatabaseTablesEnum::ReferralCustomSettings->tableName())->ignore($this->id)
            ],

            TableEnum::PackageId->dbName() => [
                'required', 'numeric',
                new ExistsItem(ReferralRewardPackage::class, __('thisApp.Errors.Referral.ReferralRewardPackageNotFound')),
            ],

        ];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        return $this->rulesStore();
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {
        return [
            TableEnum::Id->dbName() => [new ExistsItem(model::class)],
        ];
    }

    /******************** Action rules END *********************/

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->addPadToArrayVal(
            [
                TableEnum::UserId->dbName()         => trans('thisApp.UserId'),
                TableEnum::PackageId->dbName()      => trans('thisApp.AdminPages.Referral.RewardPackage'),
                TableEnum::PrivateNote->dbName()    => trans('thisApp.PrivateNote'),
            ]
        );
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        //
    }
}
