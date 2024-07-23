<?php

namespace App\Http\Resources\BackOffice\Domains;

use App\Enums\Database\Tables\DomainHolderAccountsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class DomainHolderAccountResource extends ApiResponseResource
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
            TableEnum::Id->dbName()             => (int) $this->id,
            TableEnum::DomainHolderId->dbName() => (int) $this->domain_holder_id,
            TableEnum::Username->dbName()       => $this->username,
            TableEnum::Email->dbName()          => $this->email,
            TableEnum::IsActive->dbName()       => (bool) $this->is_active,
            TableEnum::Descr->dbName()          => $this->descr,

            'domain_holder_url'                 => $this->domainHolder->url,
        ];
    }
}
