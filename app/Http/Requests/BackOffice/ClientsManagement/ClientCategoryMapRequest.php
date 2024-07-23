<?php

namespace App\Http\Requests\BackOffice\ClientsManagement;

use App\Enums\Database\Tables\ClientCategoryMapsTableEnum as TableEnum;
use App\Enums\Users\ClientCategoryMapTypesEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\BackOffice\ClientsManagement\ClientCategoryMap as model;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\Database\UniqueSuperKey;
use Illuminate\Validation\Rule;

class ClientCategoryMapRequest extends SuperRequest
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

            TableEnum::RoleId->dbName() => [
                'bail',
                'required', 'numeric',
                new ExistsItem(ClientCategory::class),
            ],

            TableEnum::MapType->dbName() => [
                'bail',
                'required',
                Rule::in(ClientCategoryMapTypesEnum::names()),
            ],

            TableEnum::ItemValue->dbName() => [
                'required',
                new UniqueSuperKey(model::class, $this[TableEnum::Id->dbName()], [
                    TableEnum::RoleId->dbName() => $this[TableEnum::RoleId->dbName()],
                    TableEnum::MapType->dbName() => $this[TableEnum::MapType->dbName()],
                    TableEnum::ItemValue->dbName() => $this[TableEnum::ItemValue->dbName()],
                ]),
            ],

            TableEnum::Priority->dbName() => ['numeric', 'min:1'],

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
        return $this->rulesStore();
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {

        return [
            TableEnum::Id->dbName() => [new ExistsItem(model::class)],
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
                TableEnum::RoleId->dbName()     => trans('thisApp.ClientCategory'),
                TableEnum::MapType->dbName()    => trans('thisApp.AdminPages.ClientsManagement.MapType'),
                TableEnum::ItemValue->dbName()      => trans('thisApp.AdminPages.ClientsManagement.Value'),
                TableEnum::Priority->dbName()   => trans('thisApp.Priority'),
                TableEnum::IsActive->dbName()   => trans('general.isActive'),
                TableEnum::Descr->dbName()      => trans('general.Description'),
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
            TableEnum::IsActive->dbName() =>  CastEnum::Boolean->cast($this->input(TableEnum::IsActive->dbName())),
        ]);
    }
}
