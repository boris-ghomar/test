<?php

namespace App\Http\Resources\BackOffice\PostGrouping;

use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class PostSpacePermissionResource extends ApiResponseResource
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
            TableEnum::Id->dbName()                 => $this[TableEnum::Id->dbName()],
            TableEnum::PostSpaceId->dbName()        => (int) $this[TableEnum::PostSpaceId->dbName()],
            TableEnum::ClientCategoryId->dbName()   => (int) $this[TableEnum::ClientCategoryId->dbName()],
            TableEnum::PostAction->dbName()         => $this[TableEnum::PostAction->dbName()],
            TableEnum::Descr->dbName()              => $this[TableEnum::Descr->dbName()],
            TableEnum::IsActive->dbName()           => $this[TableEnum::IsActive->dbName()],
        ];
    }
}
