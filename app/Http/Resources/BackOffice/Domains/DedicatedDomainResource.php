<?php

namespace App\Http\Resources\BackOffice\Domains;

use App\Enums\Database\Tables\DedicatedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Http\Resources\ApiResponseResource;

class DedicatedDomainResource extends ApiResponseResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $domainName = $this->domain_name;
        if (empty($domainName)) {
            // Data came from controller actions

            $domain = $this->domain;
            if (!is_null($domain))
                $domainName = $domain[DomainsTableEnum::Name->dbName()];
        }

        return [
            TableEnum::Id->dbName()     => (int) $this[TableEnum::Id->dbName()],
            TableEnum::Name->dbName()   => $this[TableEnum::Name->dbName()],
            TableEnum::Descr->dbName()  => $this[TableEnum::Descr->dbName()],

            'domain_name'   => $domainName,
        ];
    }
}
