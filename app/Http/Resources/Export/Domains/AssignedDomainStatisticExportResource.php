<?php

namespace App\Http\Resources\Export\Domains;

use App\Enums\Database\Tables\AssignedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\HHH_Library\Export\Traits\FormatExcelColumns;
use App\Http\Resources\ApiResponseResource;
use App\Models\BackOffice\Domains\AssignedDomain;
use App\Models\User;

class AssignedDomainStatisticExportResource extends ApiResponseResource
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

        $clientsCount = AssignedDomain::where(TableEnum::DomainId->dbName(), $this[TableEnum::DomainId->dbName()])
            ->where(TableEnum::FakeAssigned->dbName(), $this[TableEnum::FakeAssigned->dbName()])
            ->count();

        $reportsCount = AssignedDomain::where(TableEnum::DomainId->dbName(), $this[TableEnum::DomainId->dbName()])
            ->where(TableEnum::FakeAssigned->dbName(), $this[TableEnum::FakeAssigned->dbName()])
            ->where(TableEnum::Reported->dbName(), 1)
            ->count();

        return [
            $this->cellStyleLeft($this->domain_name),
            $this->cellStyleCenter($this[TableEnum::ClientTrustScore->dbName()]),
            $this->cellStyleCenter($status),
            $this->cellStyleCenter(number_format($clientsCount)),
            $this->cellStyleCenter($this->domain_public ? "Yes" : "No"),
            $this->cellStyleCenter($this->domain_suspicious ? "Yes" : "No"),
            $this->cellStyleCenter($this->domain_reported ? "Yes" : "No"),
            $this->cellStyleCenter(number_format($reportsCount)),
            $this->cellStyleCenter($this[TableEnum::FakeAssigned->dbName()] ? "Yes" : "No"),
            $this->cellStyleCenter($user->convertUTCToLocalTime($this[DomainsTableEnum::AnnouncedAt->dbName()])),
            $this->cellStyleCenter($user->convertUTCToLocalTime($this[DomainsTableEnum::BlockedAt->dbName()])),
        ];
    }
}
