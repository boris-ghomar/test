<?php

namespace App\Http\Resources\Export\Domains;

use App\Enums\Database\Tables\AssignedDomainsTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\HHH_Library\Export\Traits\FormatExcelColumns;
use App\Http\Resources\ApiResponseResource;
use App\Models\BackOffice\Domains\AssignedDomain;

class ReportedDomainExportResource extends ApiResponseResource
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
        $reportsCount = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $this[TableEnum::Id->dbName()])
            ->where(AssignedDomainsTableEnum::Reported->dbName(), 1)
            ->count();

        $domainName = $this[TableEnum::Name->dbName()];

        $desktopUrl = sprintf("https://www.%s/fa", $domainName);
        $mobileUrl = sprintf("https://m.%s/fa", $domainName);

        return [
            $this->cellStyleLeft($domainName),
            $this->cellStyleLeft($desktopUrl),
            $this->cellStyleLeft($mobileUrl),
            $this->cellStyleCenter(number_format($reportsCount)),
        ];
    }
}
