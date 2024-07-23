<?php

namespace App\Http\Requests\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralRewardItemsTableEnum as TableEnum;
use App\Enums\Referral\ReferralRewardTypeEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Referral\ReferralRewardItem as model;
use App\Models\BackOffice\Referral\ReferralRewardItem;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Rules\BackOffice\Referral\ReferralRewardItemDeleteRule;
use App\Rules\BackOffice\Referral\ReferralRewardItemUpdateRule;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\Database\UniqueSuperKey;
use App\Rules\General\Restriction\MaximumAllowedDecimalsRule;
use Illuminate\Validation\Rule;

class ReferralRewardItemRequest extends SuperRequest
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
        $rules = [

            TableEnum::PackageId->dbName() => [
                'required', 'numeric',
                new ExistsItem(ReferralRewardPackage::class, __('thisApp.Errors.Referral.ReferralRewardPackageNotFound'))
            ],

            TableEnum::Name->dbName() => [
                'required',
                new UniqueSuperKey(
                    model::class,
                    $this[TableEnum::Id->dbName()],
                    [
                        TableEnum::PackageId->dbName() => $this[TableEnum::PackageId->dbName()],
                        TableEnum::Name->dbName() => $this[TableEnum::Name->dbName()],
                    ]
                ),
            ],

            TableEnum::DisplayName->dbName() => ['required'],

            TableEnum::Type->dbName() => [
                'required',
                Rule::in(ReferralRewardTypeEnum::names())
            ],

            TableEnum::Percentage->dbName() => [
                'bail', 'required', 'numeric',
                'gte:0', 'max:100',
                new MaximumAllowedDecimalsRule(2)
            ],

            TableEnum::IsActive->dbName() => ['required', 'boolean'],
            TableEnum::DisplayPriority->dbName() => ['numeric', 'min:1'],
            TableEnum::PaymentPriority->dbName() => ['numeric', 'min:1'],
        ];

        if ($this->input(TableEnum::Type->dbName()) == ReferralRewardTypeEnum::Bonus->name) {
            $rules[TableEnum::BonusId->dbName()] = ['required'];
        }

        return $rules;
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        $referralRewardItemUpdateRule = new ReferralRewardItemUpdateRule(ReferralRewardItem::find($this->id));

        $rules = $this->rulesStore();

        foreach ($this->getCalculatingFields() as $field) {

            if (isset($rules[$field]))
                array_push($rules[$field], $referralRewardItemUpdateRule);
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
            TableEnum::Id->dbName() => [
                new ExistsItem(model::class),
                new ReferralRewardItemDeleteRule
            ],

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

            TableEnum::PackageId->dbName(),
            TableEnum::Type->dbName(),
            TableEnum::BonusId->dbName(),
            TableEnum::Percentage->dbName(),
            TableEnum::IsActive->dbName(),
            TableEnum::PaymentPriority->dbName(),
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
                TableEnum::PackageId->dbName()          => trans('thisApp.AdminPages.Referral.RewardPackage'),
                TableEnum::Name->dbName()               => trans('general.Name'),
                TableEnum::DisplayName->dbName()        => trans('thisApp.DisplayName'),
                TableEnum::Type->dbName()               => trans('thisApp.AdminPages.Referral.RewardType'),
                TableEnum::BonusId->dbName()            => trans('thisApp.AdminPages.Referral.BonusId'),
                TableEnum::Percentage->dbName()         => trans('thisApp.AdminPages.Referral.RewardPercentage'),
                TableEnum::IsActive->dbName()           => trans('general.isActive'),
                TableEnum::DisplayPriority->dbName()    => trans('thisApp.DisplayPriority'),
                TableEnum::PaymentPriority->dbName()    => trans('thisApp.PaymentPriority'),
                TableEnum::PrivateNote->dbName()        => trans('thisApp.PrivateNote'),
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
