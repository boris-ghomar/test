<?php

namespace App\Http\Resources\BackOffice\PeronnelManagement;

use App\Enums\Database\Tables\RolesTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class PersonnelRoleResource extends ApiResponseResource
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
            TableEnum::Name->dbName()       => $this[TableEnum::Name->dbName()],
            TableEnum::IsActive->dbName()   => (bool) $this[TableEnum::IsActive->dbName()],
            TableEnum::Descr->dbName()      => $this[TableEnum::Descr->dbName()],
        ];
    }
}
