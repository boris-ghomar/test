<?php

namespace App\Http\Resources\Export\Domains;

use App\Enums\Database\Tables\DomainHolderAccountsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainHoldersTableEnum;
use App\HHH_Library\Export\Traits\FormatExcelColumns;
use App\Http\Resources\ApiResponseResource;


class DomainHolderAccountExportResource extends ApiResponseResource
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
        return [
            $this->cellStyleLeft($this->domainHolder[DomainHoldersTableEnum::Name->dbName()]), // domainHolder
            $this->cellStyleLeft($this->domainHolder[DomainHoldersTableEnum::Url->dbName()]), // domainHolder->url
            $this->cellStyleLeft($this[TableEnum::Username->dbName()]), // username
            $this->cellStyleLeft($this[TableEnum::Email->dbName()]), // email
            $this->cellStyleCenter($this[TableEnum::IsActive->dbName()] ? "Yes" : "No"), // is_active
            $this->cellStyleLeft($this[TableEnum::Descr->dbName()]), // descr

        ];
    }
}
