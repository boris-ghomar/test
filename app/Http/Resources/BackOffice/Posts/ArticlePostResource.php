<?php

namespace App\Http\Resources\BackOffice\Posts;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Http\Resources\ApiResponseResource;

class ArticlePostResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $editBtn = sprintf(
            '<a target="_blank" href="%s" type="button" class="btn btn-outline-primary">%s</a>',
            $this->EditUrl,
            __('general.buttons.FullEdit')
        );

        $viewBtn = sprintf(
            '<a target="_blank" href="%s" type="button" class="btn btn-outline-success">%s</a>',
            $this->DisplayUrl,
            __('general.buttons.View')
        );

        $usersIdKey = UsersTableEnum::Id->dbName();

        $author = $this->author()->select($usersIdKey)->withTrashed()->first();
        $authorId = is_null($author) ? -1 : $author[$usersIdKey];

        $editor = $this->editor()->select($usersIdKey)->withTrashed()->first();
        $editorId = is_null($editor) ? -1 : $editor[$usersIdKey];

        return [
            TableEnum::Id->dbName()                         => (int) $this[TableEnum::Id->dbName()],
            TableEnum::PostSpaceId->dbName()                => (int) $this[TableEnum::PostSpaceId->dbName()],
            TableEnum::Title->dbName()                      => $this[TableEnum::Title->dbName()],
            TableEnum::ShortenedContentForTable->dbName()   => $this[TableEnum::ShortenedContentForTable->dbName()],
            TableEnum::IsPublished->dbName()                => $this[TableEnum::IsPublished->dbName()],
            TableEnum::Views->dbName()                      => number_format($this[TableEnum::Views->dbName()]),
            TableEnum::PrivateNote->dbName()                => $this[TableEnum::PrivateNote->dbName()],
            TimestampsEnum::CreatedAt->dbName()             => $this[TimestampsEnum::CreatedAt->dbName()],
            TableEnum::AuthorId->dbName()                   => (int) $authorId,
            TableEnum::ContentUpdatedAt->dbName()           => $this[TableEnum::ContentUpdatedAt->dbName()],
            TableEnum::EditorId->dbName()                   => (int) $editorId,
            'edit_btn'                                      => $editBtn,
            'view_btn'                                      => $viewBtn,
        ];
    }
}
