<?php

namespace App\Http\Requests\BackOffice\Posts;

use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Posts\PinnedPost as model;
use App\Models\BackOffice\Posts\Post;
use App\Rules\General\Database\ExistsItem;

class PinnedPostRequest extends SuperRequest
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
            TableEnum::Id->dbName() => ['required', 'numeric', new ExistsItem(Post::class)],
        ];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        return [
            TableEnum::Id->dbName() => ['required', 'numeric', new ExistsItem(model::class)],
            TableEnum::PinNumber->dbName() => ['required', 'numeric', 'min:1'],
        ];
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
                TableEnum::Id->dbName()  => trans('general.ID'),
                TableEnum::PinNumber->dbName()  => trans('thisApp.AdminPages.Posts.PinNumber'),
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
        //
    }
}
