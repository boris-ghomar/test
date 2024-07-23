<?php

namespace App\Http\Resources\BackOffice\PostGrouping;

use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class PostGroupsDisplayPositionResource extends ApiResponseResource
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
            TableEnum::ParentId->dbName()       => (int) $this[TableEnum::ParentId->dbName()],
            TableEnum::Title->dbName()          => $this[TableEnum::Title->dbName()],
            TableEnum::Position->dbName()       => $this[TableEnum::Position->dbName()],
            TableEnum::IsActive->dbName()       => $this[TableEnum::IsActive->dbName()],
        ];
    }
}
