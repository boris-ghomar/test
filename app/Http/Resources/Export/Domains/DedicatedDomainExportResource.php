<?php

namespace App\Http\Resources\Export\Domains;

use App\Enums\Database\Tables\DedicatedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\HHH_Library\Export\Traits\FormatExcelColumns;
use App\Http\Resources\ApiResponseResource;

class DedicatedDomainExportResource extends ApiResponseResource
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
        $domain = $this->domain;
        $domainName = $domain[DomainsTableEnum::Name->dbName()];

        $desktopUrl = sprintf("https://www.%s/fa", $domainName);
        $mobileUrl = sprintf("https://m.%s/fa", $domainName);

        return [
            $this->cellStyleLeft($this[TableEnum::Name->dbName()]),
            $this->cellStyleLeft($domainName),
            $this->cellStyleLeft($desktopUrl),
            $this->cellStyleLeft($mobileUrl),
            $this->cellStyleLeft($this[TableEnum::Descr->dbName()]),
        ];
    }
}
