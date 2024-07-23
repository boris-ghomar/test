<?php

namespace App\Http\Requests\BackOffice\PeronnelManagement;

use App\Enums\Database\Tables\RolesTableEnum as TableEnum;
use App\Enums\SystemReserved\AdminRoleReservedEnum;
use App\Enums\Users\RoleTypesEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole as model;
use App\Rules\BackOffice\Role\HasUser;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\Database\UniqueSuperKey;
use App\Rules\General\Protection\ReservedNameDelete;
use App\Rules\General\Protection\ReservedNameEdit;
use App\Rules\General\Protection\ReservedNameStore;
use App\Rules\General\StringPattern\EnglishString;
use Illuminate\Support\Facades\Validator;

class PersonnelRoleRequest extends SuperRequest
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
            TableEnum::Name->dbName() => [
                'bail',
                'required',
                new ReservedNameStore(AdminRoleReservedEnum::names()),
                new ReservedNameStore(AdminRoleReservedEnum::values()),
                new UniqueSuperKey(model::class, $this[TableEnum::Id->dbName()], [
                    TableEnum::Name->dbName() => $this[TableEnum::Name->dbName()],
                    TableEnum::Type->dbName() => $this[TableEnum::Type->dbName()],
                ]),
                new EnglishString,
            ],

            TableEnum::IsActive->dbName() => ['boolean'],
        ];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        $rules = $this->rulesStore();

        $rolePreviousName = model::find($this->id)->name;

        $rules[TableEnum::Name->dbName()] = [
            'bail',
            'required',
            new ReservedNameEdit(AdminRoleReservedEnum::names(), $rolePreviousName),
            new ReservedNameEdit(AdminRoleReservedEnum::values(), $rolePreviousName),
            new UniqueSuperKey(model::class, $this[TableEnum::Id->dbName()], [
                TableEnum::Name->dbName() => $this[TableEnum::Name->dbName()],
                TableEnum::Type->dbName() => $this[TableEnum::Type->dbName()],
            ]),
            new EnglishString,
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
        $reservedName = Validator::make($this->all(), [
            TableEnum::Name->dbName() => [
                new ReservedNameDelete(AdminRoleReservedEnum::names()),
                new ReservedNameDelete(AdminRoleReservedEnum::values()),
            ],
        ])->stopOnFirstFailure(true);

        $reservedName->validate();

        return [
            TableEnum::Id->dbName()    => [new ExistsItem(model::class), new HasUser()],
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
                TableEnum::Name->dbName()       => trans('general.Name'),
                TableEnum::IsActive->dbName()   => trans('general.isActive'),
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
            TableEnum::Type->dbName() =>  RoleTypesEnum::AdminPanel->name,
            TableEnum::IsActive->dbName() =>  filter_var($this->input(TableEnum::IsActive->dbName()), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
