<?php

namespace App\Http\Resources\BackOffice\Domains;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Http\Resources\ApiResponseResource;
use App\Models\User;

class AssignedDomainResource extends ApiResponseResource
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

        return [
            TableEnum::Id->dbName()                     => (int) $this[TableEnum::Id->dbName()],
            TableEnum::UserId->dbName()                 => (int) $this[TableEnum::UserId->dbName()],
            TableEnum::ClientTrustScore->dbName()       => (int) $this[TableEnum::ClientTrustScore->dbName()],
            TableEnum::DomainSuspiciousScore->dbName()  => (int) $this[TableEnum::DomainSuspiciousScore->dbName()],
            TableEnum::Reported->dbName()               => (bool) $this[TableEnum::Reported->dbName()],
            TableEnum::ReportedAt->dbName()             =>  $user->convertUTCToLocalTime($this[TableEnum::ReportedAt->dbName()]),
            TableEnum::FakeAssigned->dbName()           => (bool) $this[TableEnum::FakeAssigned->dbName()],

            TimestampsEnum::CreatedAt->dbName()     => $user->convertUTCToLocalTime($this[TimestampsEnum::CreatedAt->dbName()]),

            DomainsTableEnum::Public->dbName()      => (bool) $this[DomainsTableEnum::Public->dbName()],
            DomainsTableEnum::Suspicious->dbName()  => (bool) $this[DomainsTableEnum::Suspicious->dbName()],
            DomainsTableEnum::AnnouncedAt->dbName() => $user->convertUTCToLocalTime($this[DomainsTableEnum::AnnouncedAt->dbName()]),
            DomainsTableEnum::BlockedAt->dbName()   => $user->convertUTCToLocalTime($this[DomainsTableEnum::BlockedAt->dbName()]),


            'username'      => $this->username,
            'domain_name'   => $this->domain_name,
            'domain_status' => $this->domain_status,

            'current_trust_score'               => (int) $this->current_trust_score,
            'current_domain_suspicious_score'   => (int) $this->current_domain_suspicious_score,

        ];
    }
}
