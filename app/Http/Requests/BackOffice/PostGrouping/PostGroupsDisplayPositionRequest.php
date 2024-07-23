<?php

namespace App\Http\Requests\BackOffice\PostGrouping;

use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\PostGrouping\PostGroupsDisplayPosition as model;


class PostGroupsDisplayPositionRequest extends SuperRequest
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

            TableEnum::Position->dbName() => ['required', 'numeric', 'min:1'],
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
                TableEnum::Position->dbName()    => trans('thisApp.AdminPages.PostGrouping.Position'),
                TableEnum::IsActive->dbName()    => trans('general.isActive'),
            ]
        );
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            TableEnum::IsActive->dbName()       =>  TableEnum::IsActive->cast($this->input(TableEnum::IsActive->dbName())),
        ]);
    }
}
