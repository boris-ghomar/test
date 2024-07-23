<?php

namespace App\Http\Requests\BackOffice\ClientsManagement;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\PermissionRoleTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\ClientsManagement\ClientPermissionCategory as model;

class ClientPermissionCategoryRequest extends SuperRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ($this->method() == HttpMethodEnum::PUT->name)
            && $this->user()->can(PermissionAbilityEnum::update->name, model::class);
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
        return [
            TableEnum::PermissionId->dbName() => ['bail', 'required'],
            TableEnum::RoleId->dbName() => ['bail', 'required'],
            TableEnum::IsActive->dbName() => ['boolean'],
        ];
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {
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
                TableEnum::PermissionId->dbName()   => trans('general.Permission'),
                TableEnum::RoleId->dbName()         => trans('general.Role'),
                TableEnum::IsActive->dbName()       => trans('general.isActive'),
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
            TableEnum::IsActive->dbName() =>  filter_var($this->input(TableEnum::IsActive->dbName()), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
