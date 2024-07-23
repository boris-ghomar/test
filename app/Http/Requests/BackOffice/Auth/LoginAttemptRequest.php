<?php

namespace App\Http\Requests\BackOffice\Auth;

use App\Enums\Database\Tables\UsersTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use Illuminate\Foundation\Http\FormRequest;

class LoginAttemptRequest extends FormRequest
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
            TableEnum::Username->dbName() => ['required'],
            TableEnum::Password->dbName() => ['required'],
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
                TableEnum::Username->dbName()    => trans('auth.custom.Username'),
                TableEnum::Password->dbName()    => trans('auth.custom.Password'),
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

        $this->merge([
            'remember' => $this->input('remember', "off") == "on" ? true : false,
        ]);
    }
}
