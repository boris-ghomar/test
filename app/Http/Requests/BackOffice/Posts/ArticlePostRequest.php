<?php

namespace App\Http\Requests\BackOffice\Posts;

use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\HHH_Library\general\php\Enums\SeoMetaTagsEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\Posts\ArticlePost as model;
use App\Models\BackOffice\Posts\Post;
use App\Rules\General\Database\ExistsInModel;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\Database\UniqueInModel;

class ArticlePostRequest extends SuperRequest
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
        $metaTitle = SeoMetaTagsEnum::MetaTitle;

        return [

            TableEnum::PostSpaceId->dbName() => [
                'required', 'numeric',
                new ExistsInModel(PostSpace::class, PostGroupsTableEnum::Id->dbName())
            ],

            TableEnum::Title->dbName() => [
                'required',
                new UniqueInModel(Post::class, null),
                'min:' . $metaTitle->minLength(),
                'max:' . $metaTitle->maxLength(),
            ],
        ];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        $isApiRequest = $this->is('api/*');

        return $isApiRequest ? $this->rulesUpdateApi() : $this->rulesUpdateWeb();
    }

    /**
     * Rules that only require in API request
     *
     * @return array
     */
    public function rulesUpdateApi(): array
    {
        $metaTitle = SeoMetaTagsEnum::MetaTitle;

        return [

            TableEnum::AuthorId->dbName() => [
                'required', 'numeric',
                new ExistsInModel(Personnel::class, UsersTableEnum::Id->dbName())
            ],

            TableEnum::EditorId->dbName() => [
                'nullable', 'numeric',
                new ExistsInModel(Personnel::class, UsersTableEnum::Id->dbName())
            ],

            TableEnum::PostSpaceId->dbName() => [
                'required', 'numeric',
                new ExistsInModel(PostSpace::class, PostGroupsTableEnum::Id->dbName())
            ],

            TableEnum::Title->dbName() => [
                'required',
                new UniqueInModel(Post::class, $this->input(TableEnum::Id->dbName())),
                'min:' . $metaTitle->minLength(),
                'max:' . $metaTitle->maxLength(),
            ],

            TableEnum::IsPublished->dbName() => ['required', 'boolean'],
        ];
    }

    /**
     * Rules that only require in Web request
     *
     * @return array
     */
    public function rulesUpdateWeb(): array
    {
        $tabpanel = $this->_tabpanel;

        if ($tabpanel == "Content") {

            /**
             * Web content validation will be down in contoller, beacause:
             *
             * When large data is returned back to the user,
             * Laravel tries to store this data in the playload column of
             * the sessions table, which causes the user to drop the database
             * and cause a database error.
             */
            return [];

            /**
             * The following lines in this section are only for
             *  information and coordination of the validation method.
             */
            $metaTitle = SeoMetaTagsEnum::MetaTitle;

            return [

                TableEnum::Title->dbName() => [
                    'bail', 'required',
                    new UniqueInModel(Post::class, $this->input(TableEnum::Id->dbName())),
                    'min:' . $metaTitle->minLength(),
                    'max:' . $metaTitle->maxLength(),
                ],

                TableEnum::IsPublished->dbName() => ['required', 'boolean'],
            ];
        } else if ($tabpanel == "MainPhoto") {

            $mainPhoto = ImageConfigEnum::PostMainPhoto;

            return [

                TableEnum::MainPhoto->dbName() => [
                    'bail',
                    'image',
                    "mimes:" . $mainPhoto->mimes(),
                    sprintf("dimensions:min_width=%s,min_height=%s", $mainPhoto->minWidth(), $mainPhoto->minHeight()),
                    sprintf("dimensions:max_width=%s,max_height=%s", $mainPhoto->maxWidth(), $mainPhoto->maxHeight()),
                    "min:" . $mainPhoto->minSize(),
                    "max:" . $mainPhoto->maxSize(),
                ],
            ];
        } else if ($tabpanel == "SEO") {

            $metaDescription = SeoMetaTagsEnum::MetaDescription;

            return [
                TableEnum::MetaDescription->dbName() => [
                    'bail', 'required',
                    'min:' . $metaDescription->minLength(),
                    'max:' . $metaDescription->maxLength(),
                ],
            ];
        }
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
                TableEnum::PostSpaceId->dbName()        => trans('thisApp.PostSpace'),
                TableEnum::Title->dbName()              => trans('general.Title'),
                TableEnum::Content->dbName()            => trans('general.Content'),
                TableEnum::MainPhoto->dbName()          => trans('thisApp.AdminPages.Posts.MainPhoto'),
                TableEnum::MetaDescription->dbName()    => trans('general.SeoMetaTags.MetaDescription.Title'),
                TableEnum::IsPublished->dbName()        => trans('general.IsPublished'),
                TableEnum::PrivateNote->dbName()        => trans('thisApp.PrivateNote'),
                TableEnum::AuthorId->dbName()           => trans('thisApp.AdminPages.Posts.Author'),
                TableEnum::EditorId->dbName()           => trans('thisApp.AdminPages.Posts.EditedBy'),
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
        // IsPublished
        $isPublished = TableEnum::IsPublished;
        if ($this->has($isPublished->dbName())) {

            $this->merge([
                $isPublished->dbName() =>  $isPublished->cast($this->input($isPublished->dbName())),
            ]);
        }

        // EditedBy
        $editedBy = TableEnum::EditorId;
        if ($this->has($editedBy->dbName())) {

            $value = $this->input($editedBy->dbName());
            if ($value < 1) {

                $this->merge([
                    $editedBy->dbName() =>  null,
                    TableEnum::ContentUpdatedAt->dbName() =>  null,
                ]);
            }
        }
    }
}
