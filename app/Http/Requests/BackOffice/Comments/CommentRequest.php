<?php

namespace App\Http\Requests\BackOffice\Comments;

use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Comments\Comment as model;
use App\Models\BackOffice\Posts\Post;
use App\Models\User;
use App\Rules\General\Database\ExistsItem;

class CommentRequest extends SuperRequest
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

            TableEnum::PostId->dbName() => [
                'required', 'numeric',
                new ExistsItem(Post::class, __('thisApp.Errors.Comments.PostNotFound')),
            ],
            TableEnum::UserId->dbName() => [
                'required', 'numeric',
                new ExistsItem(User::class, __('thisApp.Errors.Comments.UserNotFound')),
            ],
            TableEnum::Comment->dbName() => ['required'],
            TableEnum::IsApproved->dbName() => ['required', 'boolean'],
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
                TableEnum::UserId->dbName()         => trans('thisApp.UserId'),
                TableEnum::Comment->dbName()        => trans('thisApp.PostActions.Comment'),
                TableEnum::IsApproved->dbName()     => trans('thisApp.IsApproved'),
                TableEnum::PostId->dbName()         => trans('thisApp.PostId'),
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
            TableEnum::Comment->dbName()    =>  strip_tags($this->input(TableEnum::Comment->dbName())),
            TableEnum::IsApproved->dbName() =>  TableEnum::IsApproved->cast($this->input(TableEnum::IsApproved->dbName())),
        ]);
    }
}
