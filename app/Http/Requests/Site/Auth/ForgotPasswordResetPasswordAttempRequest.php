<?php

namespace App\Http\Requests\Site\Auth;

use App\HHH_Library\general\php\Enums\HttpMethodEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Rules\General\StringPattern\EnglishStringUsernameFormat;
use App\Rules\General\StringPattern\MinOneLowercase;
use App\Rules\General\StringPattern\MinOneNumber;
use App\Rules\General\StringPattern\MinOneUppercase;

class ForgotPasswordResetPasswordAttempRequest extends SuperRequest
{


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->method() == HttpMethodEnum::POST->name;
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

            'password' => [
                'required',
                'min:' . config('hhh_config.validation.minLength.password'),
                'max:' . config('hhh_config.validation.maxLength.password'),
                new EnglishStringUsernameFormat,
                new MinOneLowercase,
                new MinOneUppercase,
                new MinOneNumber,
            ],

            'password_confirmation' => [
                'required',
                'same:' . 'password',
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
                'password'    => trans('passwords.newPassword'),
                'password_confirmation'    => trans('passwords.newPasswordConfirmation'),
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
