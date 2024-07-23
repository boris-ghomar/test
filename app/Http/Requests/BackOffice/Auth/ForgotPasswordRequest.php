<?php

namespace App\Http\Requests\BackOffice\Auth;

use App\Enums\Database\Tables\UsersTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;
use App\Http\Requests\SuperClasses\SuperRequest;


class ForgotPasswordRequest extends SuperRequest
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
            TableEnum::Email->dbName() => ['required', 'email:rfc,strict,dns',],
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
                TableEnum::Email->dbName()    => trans('auth.custom.Email'),
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
