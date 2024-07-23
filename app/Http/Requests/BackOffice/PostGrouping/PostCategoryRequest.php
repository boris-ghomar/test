<?php

namespace App\Http\Requests\BackOffice\PostGrouping;

use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\PostGrouping\PostCategory as model;
use App\Models\BackOffice\PostGrouping\PostGroup;
use App\Rules\BackOffice\PostGrouping\PostCategoryHasSpace;
use App\Rules\BackOffice\PostGrouping\PostCategoryHasSubCategory;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\Database\UniqueInModel;
use App\Rules\General\Database\ValidParentId;


class PostCategoryRequest extends SuperRequest
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


            TableEnum::Title->dbName() => [
                'required',
                new UniqueInModel(PostGroup::class, $this->input(TableEnum::Id->dbName()))
            ],
            TableEnum::ParentId->dbName() => [
                'required', 'numeric',
                new ValidParentId($this->input(TableEnum::Id->dbName()), model::class)
            ],
            TableEnum::IsSpace->dbName() => ['required', 'boolean'],
            TableEnum::IsActive->dbName() => ['required', 'boolean'],
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
            TableEnum::Id->dbName()    => [
                'bail',
                new ExistsItem(model::class),
                new PostCategoryHasSubCategory,
                new PostCategoryHasSpace,
            ],
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
                TableEnum::ParentId->dbName()       => trans('thisApp.AdminPages.PostGrouping.ParentId'),
                TableEnum::Title->dbName()          => trans('general.Title'),
                TableEnum::Description->dbName()    => trans('thisApp.AdminPages.PostGrouping.Description'),
                TableEnum::Photo->dbName()          => trans('thisApp.AdminPages.PostGrouping.Photo'),
                TableEnum::IsActive->dbName()       => trans('general.isActive'),
                TableEnum::PrivateNote->dbName()    => trans('thisApp.PrivateNote'),
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
            TableEnum::IsActive->dbName()       =>  TableEnum::IsActive->cast($this->input(TableEnum::IsActive->dbName())),
            TableEnum::IsSpace->dbName()        =>  false,
            TableEnum::IsPublicSpace->dbName()  =>  false,
        ]);
    }
}
