<?php

namespace App\Http\Requests\BackOffice\PostGrouping;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\AccessControl\PostActionsEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum as TableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\PostGrouping\PostSpacePermission as model;
use App\Rules\General\Database\ExistsInModel;
use Illuminate\Validation\Rule;

class PostSpacePermissionRequest extends SuperRequest
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
            TableEnum::PostSpaceId->dbName() => [
                'required', 'numeric',
                new ExistsInModel(PostSpace::class, PostGroupsTableEnum::Id->dbName())
            ],

            TableEnum::ClientCategoryId->dbName() => [
                'required', 'numeric',
                new ExistsInModel(ClientCategory::class, RolesTableEnum::Id->dbName())
            ],

            TableEnum::PostAction->dbName() => [
                'required',
                Rule::in(PostActionsEnum::names())
            ],

            TableEnum::IsActive->dbName() => ['required', 'boolean'],
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
            TableEnum::IsActive->dbName() =>  TableEnum::IsActive->cast($this->input(TableEnum::IsActive->dbName())),
        ]);
    }
}
