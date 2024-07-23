<?php

namespace App\Http\Resources\Export\Domains;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\HHH_Library\Export\Traits\FormatExcelColumns;
use App\Http\Resources\ApiResponseResource;
use App\Models\User;

class AssignedDomainExportResource extends ApiResponseResource
{
    use FormatExcelColumns;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::authUser();

        $status = $this->domain_status;
        /** @var DomainStatusEnum  $statusCase */
        $statusCase = DomainStatusEnum::getCase($status);
        $status = is_null($statusCase) ? $status : $statusCase->translate();


        return [
            $this->cellStyleCenter($this[TableEnum::UserId->dbName()]),
            $this->cellStyleLeft($this->username),
            $this->cellStyleLeft($this->domain_name),
            $this->cellStyleCenter($this[TableEnum::ClientTrustScore->dbName()]),
            $this->cellStyleCenter($this->current_trust_score),
            $this->cellStyleCenter($this[TableEnum::DomainSuspiciousScore->dbName()]),
            $this->cellStyleCenter($this->current_domain_suspicious_score),
            $this->cellStyleCenter($status),
            $this->cellStyleCenter($this[DomainsTableEnum::Public->dbName()] ? "Yes" : "No"),
            $this->cellStyleCenter($this[DomainsTableEnum::Suspicious->dbName()] ? "Yes" : "No"),
            $this->cellStyleCenter($this[TableEnum::Reported->dbName()] ? "Yes" : "No"),
            $this->cellStyleCenter($this[TableEnum::FakeAssigned->dbName()] ? "Yes" : "No"),
            $this->cellStyleCenter($user->convertUTCToLocalTime($this[DomainsTableEnum::AnnouncedAt->dbName()])),
            $this->cellStyleCenter($user->convertUTCToLocalTime($this[TimestampsEnum::CreatedAt->dbName()])),
            $this->cellStyleCenter($user->convertUTCToLocalTime($this[TableEnum::ReportedAt->dbName()])),
            $this->cellStyleCenter($user->convertUTCToLocalTime($this[DomainsTableEnum::BlockedAt->dbName()])),
        ];
    }
}
