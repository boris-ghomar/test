<?php

namespace App\Http\Requests\BackOffice\PeronnelManagement;

use App\Enums\Database\Tables\PersonnelExtrasTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Enums\Users\UsersTypesEnum;
use App\HHH_Library\general\php\Enums\GendersEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\PeronnelManagement\Personnel as model;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\BackOffice\PeronnelManagement\PersonnelExtra;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;
use App\Models\User;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\Database\UniqueSuperKey;
use App\Rules\General\StringPattern\EnglishStringUsernameFormat;
use Illuminate\Validation\Rule;

class PersonnelRequest extends SuperRequest
{

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

            UsersTableEnum::Username->dbName() => [
                'bail',
                'required',
                new EnglishStringUsernameFormat,
                "min:" . config('hhh_config.validation.minLength.username'),
                "max:" . config('hhh_config.validation.maxLength.username'),
                new UniqueSuperKey(User::class, $this[UsersTableEnum::Id->dbName()], [
                    UsersTableEnum::Username->dbName() => $this[UsersTableEnum::Username->dbName()],
                    UsersTableEnum::Type->dbName() => UsersTypesEnum::Personnel->name,
                ]),

            ],
            UsersTableEnum::Email->dbName() => [
                'bail', 'required', 'email:rfc,strict',
                "min:" . config('hhh_config.validation.minLength.email'),
                "max:" . config('hhh_config.validation.maxLength.email'),
                new UniqueSuperKey(User::class, $this[UsersTableEnum::Id->dbName()], [
                    UsersTableEnum::Email->dbName() => $this[UsersTableEnum::Email->dbName()],
                    UsersTableEnum::Type->dbName() => UsersTypesEnum::Personnel->name,
                ]),
            ],
            UsersTableEnum::RoleId->dbName() => ['bail', 'required', new ExistsItem(PersonnelRole::class)],
            UsersTableEnum::Status->dbName() => ['bail', 'required', Rule::in(UsersStatusEnum::names())],


            PersonnelExtrasTableEnum::FirstName->dbName() => ['bail', 'required'],
            PersonnelExtrasTableEnum::LastName->dbName() => ['bail', 'required'],
            PersonnelExtrasTableEnum::AliasName->dbName()  => [
                'nullable',
                Rule::unique(PersonnelExtra::class)
            ],
            PersonnelExtrasTableEnum::Gender->dbName() => ['bail', 'required', Rule::in(GendersEnum::names())],
        ];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        $rules =  $this->rulesStore();

        $personnelExtraID = Personnel::find($this->id)->personnelExtra->id;

        $rules[PersonnelExtrasTableEnum::AliasName->dbName()] = [
            'nullable',
            Rule::unique(PersonnelExtra::class)->ignore($personnelExtraID)
        ];

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
                UsersTableEnum::Username->dbName()              => trans('general.UserName'),
                UsersTableEnum::Email->dbName()                 => trans('general.Email'),
                UsersTableEnum::RoleId->dbName()                => trans('general.Role'),
                UsersTableEnum::Status->dbName()                => trans('general.AccountStatus'),

                PersonnelExtrasTableEnum::FirstName->dbName()   => trans('general.FirstName'),
                PersonnelExtrasTableEnum::LastName->dbName()    => trans('general.LastName'),
                PersonnelExtrasTableEnum::AliasName->dbName()   => trans('general.AliasName'),
                PersonnelExtrasTableEnum::Gender->dbName()      => trans('general.Gender'),
                PersonnelExtrasTableEnum::Descr->dbName()       => trans('general.Description'),
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

            UsersTableEnum::Email->dbName() =>  strtolower($this[UsersTableEnum::Email->dbName()]),
            // UsersTableEnum::Type->dbName() =>  UsersTypesEnum::Personnel->name, // disabled for insert in controller for security
        ]);
    }
}
