<?php

namespace App\Http\Requests\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum as TableEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Referral\ReferralRewardPayment as model;

class ReferralRewardPaymentRequest extends SuperRequest
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
        // Disabled from controller
        return [];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        return [];
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {
        // Disabled from controller
        return [];
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
                TableEnum::Descr->dbName()     => trans('general.Description'),
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
