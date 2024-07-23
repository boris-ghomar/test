<?php

namespace App\Http\Resources\BackOffice\Domains;

use App\Enums\Database\Tables\AssignedDomainsTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;
use App\Models\BackOffice\Domains\AssignedDomain;

class ReportedDomainResource extends ApiResponseResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $reportsCount = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $this[TableEnum::Id->dbName()])
            ->where(AssignedDomainsTableEnum::Reported->dbName(), 1)
            ->count();

        return [
            TableEnum::Id->dbName()     => (int) $this[TableEnum::Id->dbName()],
            TableEnum::Name->dbName()   => $this[TableEnum::Name->dbName()],

            'ReportsCount'   => number_format($reportsCount),
        ];
    }
}
