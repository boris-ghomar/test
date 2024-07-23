<?php

namespace App\Http\Requests\BackOffice\PostGrouping;

use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Enums\Posts\TemplatesEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\PostGrouping\PostCategory;
use App\Models\BackOffice\PostGrouping\PostGroup;
use App\Models\BackOffice\PostGrouping\PostSpace as model;
use App\Rules\BackOffice\PostGrouping\PostSpaceHasPost;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\Database\UniqueInModel;
use App\Rules\General\Database\ValidParentId;
use Illuminate\Validation\Rule;

class PostSpaceRequest extends SuperRequest
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
                new ValidParentId($this->input(TableEnum::Id->dbName()), PostCategory::class)
            ],
            TableEnum::Template->dbName() => ['required', Rule::in(TemplatesEnum::names())],
            TableEnum::IsSpace->dbName() => ['required', 'boolean'],
            TableEnum::IsPublicSpace->dbName() => ['required', 'boolean'],
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
                new PostSpaceHasPost,
                new ExistsItem(model::class),
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
                TableEnum::Template->dbName()       => trans('thisApp.AdminPages.PostGrouping.Template'),
                TableEnum::Description->dbName()    => trans('thisApp.AdminPages.PostGrouping.Description'),
                TableEnum::Photo->dbName()          => trans('thisApp.AdminPages.PostGrouping.Photo'),
                TableEnum::IsPublicSpace->dbName()  => trans('thisApp.AdminPages.PostGrouping.IsPublicSpace'),
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
            TableEnum::IsSpace->dbName()        =>  true,
            TableEnum::IsActive->dbName()       =>  TableEnum::IsActive->cast($this->input(TableEnum::IsActive->dbName())),
            TableEnum::IsPublicSpace->dbName()  =>  TableEnum::IsPublicSpace->cast($this->input(TableEnum::IsPublicSpace->dbName())),
        ]);
    }
}
