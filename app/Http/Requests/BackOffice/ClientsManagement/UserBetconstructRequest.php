<?php

namespace App\Http\Requests\BackOffice\ClientsManagement;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Users\UsersStatusEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Http\Controllers\BackOffice\ClientsManagement\UserBetconstructController;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct as model;
use App\Models\User;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\User\DateTimeFormatRule;
use Illuminate\Validation\Rule;

class UserBetconstructRequest extends SuperRequest
{

    private $displayColumns;

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
        return [];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        $rules = [
            UsersTableEnum::RoleId->dbName() => ['bail', 'required', new ExistsItem(ClientCategory::class)],
            UsersTableEnum::Status->dbName() => ['bail', 'required', Rule::in(UsersStatusEnum::names())],
            UsersTableEnum::Email->dbName() => [
                'email:rfc,strict',
                "min:" . config('hhh_config.validation.minLength.email'),
                "max:" . config('hhh_config.validation.maxLength.email'),
            ],

            ClientModelEnum::FirstName->dbName() => ['required'],
            ClientModelEnum::LastName->dbName() => ['required'],
            ClientModelEnum::BirthDateStamp->dbName() => ['required', new DateTimeFormatRule],
            ClientModelEnum::IsTest->dbName() => ['required', 'boolean'],
        ];

        // Add only expected items from request
        $res = [];
        foreach ($rules as $key => $rule) {

            if (in_array($key, $this->displayColumns))
                $res[$key] = $rule;
        }

        return $res;
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {
        return [
            'id'    => [new ExistsItem(model::class)],
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
                UsersTableEnum::Id->dbName()        => __('thisApp.UserId'),
                UsersTableEnum::RoleId->dbName()    => __('general.Role'),
                UsersTableEnum::Status->dbName()    => __('general.AccountStatus'),

                ClientModelEnum::Email->dbName()            => __('general.Email'),
                ClientModelEnum::FirstName->dbName()        => __('general.FirstName'),
                ClientModelEnum::LastName->dbName()         => __('general.LastName'),
                ClientModelEnum::BirthDateStamp->dbName()   => __('general.Birthday'),
                ClientModelEnum::IsTest->dbName()           => __('bc_api.IsTest'),
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
        $customizablePageSettings = (new UserBetconstructController)->getCustomizablePageSettings();
        $this->displayColumns = $customizablePageSettings[config('hhh_config.keywords.displayColumns')];


        $isTestCol = ClientModelEnum::IsTest->dbName();

        if ($this->has($isTestCol)) {

            $this->merge([
                ClientModelEnum::IsTest->dbName()   => CastEnum::Boolean->cast($this[ClientModelEnum::IsTest->dbName()]),
            ]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Remove unexpected items from request
        foreach ($this->all() as $key => $value) {

            if (!in_array($key, $this->displayColumns))
                $this->request->remove($key);
        }

        // Modify required items
        $birthDateStampCol = ClientModelEnum::BirthDateStamp->dbName();

        if ($this->has($birthDateStampCol)) {
            $user = User::authUser();

            $birthDateTime = $this->$birthDateStampCol;
            [$birthDate] = explode(" ", $birthDateTime);
            $birthDate .= " 12:00:00"; // 12 o'clock is set to avoid time zone effects

            $this->merge([
                ClientModelEnum::BirthDateStamp->dbName()   => $user->convertLocalTimeToUTC($birthDate),
            ]);
        }
    }
}
