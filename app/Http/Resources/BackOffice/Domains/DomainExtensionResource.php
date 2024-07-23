<?php

namespace App\Http\Resources\BackOffice\Domains;

use App\Enums\Database\Tables\DomainExtensionsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class DomainExtensionResource extends ApiResponseResource
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
            TableEnum::Id->dbName()             => (int) $this[TableEnum::Id->dbName()],
            TableEnum::Name->dbName()           => $this[TableEnum::Name->dbName()],
            TableEnum::LimitedOrder->dbName()   => (bool) $this[TableEnum::LimitedOrder->dbName()],
            TableEnum::IsActive->dbName()       => (bool) $this[TableEnum::IsActive->dbName()],
            TableEnum::Descr->dbName()          => $this[TableEnum::Descr->dbName()],
        ];
    }
}
