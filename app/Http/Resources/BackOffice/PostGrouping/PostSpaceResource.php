<?php

namespace App\Http\Resources\BackOffice\PostGrouping;

use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class PostSpaceResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            TableEnum::Id->dbName()             => (int) $this[TableEnum::Id->dbName()],
            TableEnum::Title->dbName()          => $this[TableEnum::Title->dbName()],
            TableEnum::ParentId->dbName()       => (int) $this[TableEnum::ParentId->dbName()],
            TableEnum::Template->dbName()       => $this[TableEnum::Template->dbName()],
            TableEnum::Description->dbName()    => $this[TableEnum::Description->dbName()],
            TableEnum::IsPublicSpace->dbName()  => $this[TableEnum::IsPublicSpace->dbName()],
            TableEnum::IsActive->dbName()       => $this[TableEnum::IsActive->dbName()],
            TableEnum::PrivateNote->dbName()    => $this[TableEnum::PrivateNote->dbName()],
        ];
    }
}
