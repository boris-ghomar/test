<?php

namespace App\Http\Resources\BackOffice\Domains;

use App\Enums\Database\Tables\DomainCategoriesTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class DomainCategoryResource extends ApiResponseResource
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
            TableEnum::Id->dbName()                 => (int) $this[TableEnum::Id->dbName()],
            TableEnum::Name->dbName()               => $this[TableEnum::Name->dbName()],
            TableEnum::DomainAssignment->dbName()   => (bool) $this[TableEnum::DomainAssignment->dbName()],
            TableEnum::IsActive->dbName()           => (bool) $this[TableEnum::IsActive->dbName()],
            TableEnum::Descr->dbName()              => $this[TableEnum::Descr->dbName()],
        ];
    }
}
