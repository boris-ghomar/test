<?php

namespace App\Http\Requests\BackOffice\Referral;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum as TableEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Models\BackOffice\Referral\ReferralSession as model;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\User\DateTimeFormatRule;
use Illuminate\Validation\Rule;

class ReferralSessionRequest extends SuperRequest
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
                Rule::unique(DatabaseTablesEnum::ReferralSessions->tableName())->ignore($this->id)
            ],

            TableEnum::PackageId->dbName() => [
                'required', 'numeric',
                new ExistsItem(ReferralRewardPackage::class, __('thisApp.Errors.Referral.ReferralRewardPackageNotFound')),
            ],

            TableEnum::StartedAt->dbName() => ['required', new DateTimeFormatRule],
            TableEnum::FinishedAt->dbName() => ['required', new DateTimeFormatRule],

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
            TableEnum::Id->dbName() => [
                new ExistsItem(model::class),
            ],

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
                TableEnum::PackageId->dbName()                  => trans('thisApp.AdminPages.Referral.RewardPackage'),
                TableEnum::Name->dbName()                       => trans('general.Name'),
                TableEnum::Status->dbName()                     => trans('general.Status'),
                TableEnum::StartedAt->dbName()                  => trans('thisApp.StartTime'),
                TableEnum::FinishedAt->dbName()                 => trans('thisApp.FinishTime'),
                TableEnum::PrivateNote->dbName()                => trans('thisApp.PrivateNote'),
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

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        //
    }
}
