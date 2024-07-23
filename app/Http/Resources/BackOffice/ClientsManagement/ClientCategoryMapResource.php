<?php

namespace App\Http\Resources\BackOffice\ClientsManagement;

use App\Enums\Database\Tables\ClientCategoryMapsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class ClientCategoryMapResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            TableEnum::Id->dbName()         => (int) $this[TableEnum::Id->dbName()],
            TableEnum::RoleId->dbName()     => (int) $this[TableEnum::RoleId->dbName()],
            TableEnum::MapType->dbName()    => $this[TableEnum::MapType->dbName()],
            TableEnum::ItemValue->dbName()  => $this[TableEnum::ItemValue->dbName()],
            TableEnum::Priority->dbName()   => $this[TableEnum::Priority->dbName()],
            TableEnum::IsActive->dbName()   => (bool) $this[TableEnum::IsActive->dbName()],
            TableEnum::Descr->dbName()      => $this[TableEnum::Descr->dbName()],
        ];
    }
}
