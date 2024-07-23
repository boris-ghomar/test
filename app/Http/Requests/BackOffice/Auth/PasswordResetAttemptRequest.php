<?php

namespace App\Http\Requests\BackOffice\Auth;

use App\Enums\Database\Tables\PasswordResetTokenEnum;
use App\Enums\Database\Tables\UsersTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\Rules\General\StringPattern\MinOneLowercase;
use App\Rules\General\StringPattern\MinOneNumber;
use App\Rules\General\StringPattern\MinOneUppercase;
use Illuminate\Foundation\Http\FormRequest;

class PasswordResetAttemptRequest extends FormRequest
{

    use AddAttributesPad;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->method() == HttpMethodEnum::POST->name;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            PasswordResetTokenEnum::Token->dbName() => ['required'],
            TableEnum::Email->dbName() => ['required'],
            TableEnum::Password->dbName() => [
                'required',
                'min:' . config('hhh_config.validation.minLength.password'),
                'max:' . config('hhh_config.validation.maxLength.password'),
                new MinOneLowercase,
                new MinOneUppercase,
                new MinOneNumber,
            ],

            'password_confirmation' => [
                'required',
                'same:' . TableEnum::Password->dbName(),
            ],
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
                TableEnum::Email->dbName()    => trans('auth.custom.Email'),
                TableEnum::Password->dbName()    => trans('passwords.newPassword'),
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
