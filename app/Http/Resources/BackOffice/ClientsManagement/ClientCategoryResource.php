<?php

namespace App\Http\Resources\BackOffice\ClientsManagement;

use App\Enums\Database\Tables\RolesTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class ClientCategoryResource extends ApiResponseResource
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
            TableEnum::Name->dbName()           => $this[TableEnum::Name->dbName()],
            TableEnum::DisplayName->dbName()    => $this[TableEnum::DisplayName->dbName()],
            TableEnum::IsActive->dbName()       => (bool) $this[TableEnum::IsActive->dbName()],
            TableEnum::Descr->dbName()          => $this[TableEnum::Descr->dbName()],
        ];
    }
}
