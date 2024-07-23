<?php

namespace App\Http\Requests\BackOffice\Referral;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Referral\ReferralRewardPackage as model;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Rules\BackOffice\Referral\ReferralRewardPackageDeleteRule;
use App\Rules\BackOffice\Referral\ReferralRewardPackageUpdateRule;
use App\Rules\General\Database\ExistsItem;
use Illuminate\Validation\Rule;

class ReferralRewardPackageRequest extends SuperRequest
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

            TableEnum::Name->dbName() => [
                'required',
                Rule::unique(DatabaseTablesEnum::ReferralRewardPackages->tableName())->ignore($this->id)
            ],

            TableEnum::DisplayName->dbName() => ['required'],
            TableEnum::ClaimCount->dbName() => ['required', 'numeric', 'min:1', 'max:20'],
            TableEnum::IsActive->dbName() => ['required', 'boolean'],

            TableEnum::MinBetCountReferrer->dbName() => ['nullable', 'numeric', 'min:0'],
            TableEnum::MinBetOddsReferrer->dbName() => ['nullable', 'numeric', 'gte:1', 'max:400'],
            TableEnum::MinBetAmountUsdReferrer->dbName() => ['nullable', 'numeric', 'gte:0'],
            TableEnum::MinBetAmountIrrReferrer->dbName() => ['nullable', 'numeric', 'gte:0'],

            TableEnum::MinBetCountReferred->dbName() => ['nullable', 'numeric', 'min:0'],
            TableEnum::MinBetOddsReferred->dbName() => ['nullable', 'numeric', 'gte:1', 'max:400'],
            TableEnum::MinBetAmountUsdReferred->dbName() => ['nullable', 'numeric', 'gte:0'],
            TableEnum::MinBetAmountIrrReferred->dbName() => ['nullable', 'numeric', 'gte:0'],

        ];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        $referralRewardPackageInUseRule = new ReferralRewardPackageUpdateRule(ReferralRewardPackage::find($this->id));

        $rules = $this->rulesStore();

        foreach ($this->getCalculatingFields() as $field) {

            array_push($rules[$field], $referralRewardPackageInUseRule);
        }
        return $rules;
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {
        return [
            TableEnum::Id->dbName() => [new ExistsItem(model::class), new ReferralRewardPackageDeleteRule],
        ];
    }

    /******************** Action rules END *********************/

    /**
     * Get the fields involved in the calculations
     *
     * @return array
     */
    private function getCalculatingFields(): array
    {
        return [

            TableEnum::ClaimCount->dbName(),
            TableEnum::IsActive->dbName(),

            TableEnum::MinBetCountReferrer->dbName(),
            TableEnum::MinBetOddsReferrer->dbName(),
            TableEnum::MinBetAmountUsdReferrer->dbName(),
            TableEnum::MinBetAmountIrrReferrer->dbName(),

            TableEnum::MinBetCountReferred->dbName(),
            TableEnum::MinBetOddsReferred->dbName(),
            TableEnum::MinBetAmountUsdReferred->dbName(),
            TableEnum::MinBetAmountIrrReferred->dbName(),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->addPadToArrayVal(
            [
                TableEnum::Name->dbName()           => trans('general.Name'),
                TableEnum::DisplayName->dbName()    => trans('thisApp.DisplayName'),
                TableEnum::ClaimCount->dbName()     => trans('thisApp.AdminPages.Referral.ClaimCount'),
                TableEnum::Descr->dbName()          => trans('general.Description'),
                TableEnum::IsActive->dbName()       => trans('general.isActive'),

                TableEnum::MinBetCountReferrer->dbName()        => trans('thisApp.AdminPages.Referral.MinBetCount') . trans('thisApp.AdminPages.Referral.subTitle.Referrer'),
                TableEnum::MinBetOddsReferrer->dbName()         => trans('thisApp.AdminPages.Referral.MinBetOdds') . trans('thisApp.AdminPages.Referral.subTitle.Referrer'),
                TableEnum::MinBetAmountUsdReferrer->dbName()    => trans('thisApp.AdminPages.Referral.MinBetAmount') . trans('thisApp.AdminPages.Referral.subTitle.ReferrerUsd'),
                TableEnum::MinBetAmountIrrReferrer->dbName()    => trans('thisApp.AdminPages.Referral.MinBetAmount') . trans('thisApp.AdminPages.Referral.subTitle.ReferrerIrr'),

                TableEnum::MinBetCountReferred->dbName()        => trans('thisApp.AdminPages.Referral.MinBetCount') . trans('thisApp.AdminPages.Referral.subTitle.Referred'),
                TableEnum::MinBetOddsReferred->dbName()         => trans('thisApp.AdminPages.Referral.MinBetOdds') . trans('thisApp.AdminPages.Referral.subTitle.Referred'),
                TableEnum::MinBetAmountUsdReferred->dbName()    => trans('thisApp.AdminPages.Referral.MinBetAmount') . trans('thisApp.AdminPages.Referral.subTitle.ReferredUsd'),
                TableEnum::MinBetAmountIrrReferred->dbName()    => trans('thisApp.AdminPages.Referral.MinBetAmount') . trans('thisApp.AdminPages.Referral.subTitle.ReferredIrr'),

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
        $this->merge([
            TableEnum::IsActive->dbName() =>  CastEnum::Boolean->cast($this->input(TableEnum::IsActive->dbName())),
        ]);
    }
}
