<?php

namespace App\Http\Resources\BackOffice\AccessControl;

use App\Enums\Database\Tables\PermissionsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class PermissionResource extends ApiResponseResource
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
            TableEnum::Id->dbName()         => (int) $this[TableEnum::Id->dbName()],
            TableEnum::Route->dbName()      => $this[TableEnum::Route->dbName()],
            TableEnum::Ability->dbName()    => $this[TableEnum::Ability->dbName()],
            TableEnum::Type->dbName()       => $this[TableEnum::Type->dbName()],
            TableEnum::IsActive->dbName()   => (bool) $this[TableEnum::IsActive->dbName()],
            TableEnum::Descr->dbName()      => $this[TableEnum::Descr->dbName()],
        ];
    }
}
