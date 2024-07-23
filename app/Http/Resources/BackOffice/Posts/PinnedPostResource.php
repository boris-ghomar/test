<?php

namespace App\Http\Resources\BackOffice\Posts;

use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class PinnedPostResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $templateCol = PostGroupsTableEnum::Template->dbName();
        $template =  is_null($this[$templateCol]) ? $this->postSpace->$templateCol : $this->$templateCol;

        $viewBtn = sprintf(
            '<a target="_blank" href="%s" type="button" class="btn btn-outline-success">%s</a>',
            $this->DisplayUrl,
            __('general.buttons.View')
        );

        return [
            TableEnum::Id->dbName()                         => (int) $this[TableEnum::Id->dbName()],
            TableEnum::Title->dbName()                      => $this[TableEnum::Title->dbName()],
            TableEnum::PostSpaceId->dbName()                => (int) $this[TableEnum::PostSpaceId->dbName()],
            TableEnum::ShortenedContentForTable->dbName()   => $this[TableEnum::ShortenedContentForTable->dbName()],
            TableEnum::PinNumber->dbName()                  => (int) $this[TableEnum::PinNumber->dbName()],
            TableEnum::Views->dbName()                      => number_format($this[TableEnum::Views->dbName()]),
            TableEnum::PrivateNote->dbName()                => $this[TableEnum::PrivateNote->dbName()],
            $templateCol                                    => $template,
            'view_btn'                                      => $viewBtn,
        ];
    }
}
