<?php

namespace App\Http\Resources\BackOffice\Domains;

use App\Enums\Database\Tables\DomainHoldersTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class DomainHolderResource extends ApiResponseResource
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
            TableEnum::Id->dbName()         => (int) $this->id,
            TableEnum::Name->dbName()       => $this->name,
            TableEnum::Url->dbName()        => $this->url,
            TableEnum::IsActive->dbName()   => (bool) $this->is_active,
            TableEnum::Descr->dbName()      => $this->descr,
        ];
    }
}
