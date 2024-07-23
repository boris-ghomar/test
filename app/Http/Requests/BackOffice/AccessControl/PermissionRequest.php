<?php

namespace App\Http\Requests\BackOffice\AccessControl;

use App\Enums\Database\Tables\PermissionsTableEnum as TableEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\AccessControl\Permission as model;

class PermissionRequest extends SuperRequest
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
                TableEnum::Route->dbName()      => trans('thisApp.AdminPages.AccessControl.Permissions.Route'),
                TableEnum::Ability->dbName()    => trans('thisApp.AdminPages.AccessControl.Permissions.Ability'),
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
            TableEnum::IsActive->dbName() =>  filter_var($this->input(TableEnum::IsActive->dbName()), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
