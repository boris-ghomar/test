<?php

namespace App\Http\Resources\BackOffice\Domains;

use App\Enums\Database\Tables\AssignedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Http\Resources\ApiResponseResource;
use App\Models\BackOffice\Domains\AssignedDomain;
use App\Models\User;

class AssignedDomainsStatisticResource extends ApiResponseResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::authUser();

        $clientsCount = AssignedDomain::where(TableEnum::DomainId->dbName(), $this[TableEnum::DomainId->dbName()])
            ->where(TableEnum::FakeAssigned->dbName(), $this[TableEnum::FakeAssigned->dbName()])
            ->count();

        $reportsCount = AssignedDomain::where(TableEnum::DomainId->dbName(), $this[TableEnum::DomainId->dbName()])
            ->where(TableEnum::FakeAssigned->dbName(), $this[TableEnum::FakeAssigned->dbName()])
            ->where(TableEnum::Reported->dbName(), 1)
            ->count();

        return [
            TableEnum::Id->dbName()                 => (int) $this[TableEnum::Id->dbName()],
            TableEnum::ClientTrustScore->dbName()   => (int) $this[TableEnum::ClientTrustScore->dbName()],
            TableEnum::FakeAssigned->dbName()       => (bool) $this[TableEnum::FakeAssigned->dbName()],

            DomainsTableEnum::AnnouncedAt->dbName() => $user->convertUTCToLocalTime($this[DomainsTableEnum::AnnouncedAt->dbName()]),
            DomainsTableEnum::BlockedAt->dbName()   => $user->convertUTCToLocalTime($this[DomainsTableEnum::BlockedAt->dbName()]),

            'clients_count'     => number_format($clientsCount),
            'domain_public'     => (bool) $this->domain_public,
            'domain_suspicious' => (bool) $this->domain_suspicious,
            'domain_reported'   => (bool) $this->domain_reported,
            'ReportsCount'      => number_format($reportsCount),
            'domain_name'       => $this->domain_name,
            'domain_status'     => $this->domain_status,
        ];
    }
}
