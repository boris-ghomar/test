<?php

namespace App\Http\Requests\Site\Auth;

use App\Enums\Session\GeneralSessionsEnum;
use App\Enums\Users\PasswordRecoveryMethodEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;
use App\Http\Controllers\Site\Auth\Betconstruct\ForgotPasswordController;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Rules\General\StringPattern\MobileNumberRule;

class ForgotPasswordAttempRequest extends SuperRequest
{

    private $recoveryMethod = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->method() != HttpMethodEnum::POST->name)
            return false;

        $this->recoveryMethod = GeneralSessionsEnum::SiteRecoveryPasswordMethod->getSession();
        if (!in_array($this->recoveryMethod, ForgotPasswordController::getAvailablePasswordRecoveryMethods()))
            return false;

        return true;
    }

    /******************** Action rules *********************/

    /**
     * Rules for store a newly created resource in storage.
     *
     * @return array
     */
    public function rulesStore(): array
    {
        $emailKey = PasswordRecoveryMethodEnum::Email->name;
        $mobileKey = PasswordRecoveryMethodEnum::Mobile->name;

        if ($this->recoveryMethod === $emailKey) {
            return [
                $emailKey => ['required', 'email:rfc,strict,dns',],
            ];
        } else if ($this->recoveryMethod === $mobileKey) {
            return [
                $mobileKey => ['required', 'numeric', 'digits_between:8,15', new MobileNumberRule],
            ];
        }
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
        // disabled from controller
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
                PasswordRecoveryMethodEnum::Email->name     => trans('auth_site.custom.Email'),
                PasswordRecoveryMethodEnum::Mobile->name    => trans('auth_site.custom.MobileNumber'),
            ]
        );
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        //
    }
}
