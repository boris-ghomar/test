<?php

namespace App\Http\Resources\Export\Domains;

use App\Enums\Database\Tables\DomainCategoriesTableEnum;
use App\Enums\Database\Tables\DomainHolderAccountsTableEnum;
use App\Enums\Database\Tables\DomainHoldersTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\HHH_Library\Export\Traits\FormatExcelColumns;
use App\Http\Resources\ApiResponseResource;
use App\Models\User;

class DomainExportResource extends ApiResponseResource
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

        $status = $this[TableEnum::Status->dbName()];
        /** @var DomainStatusEnum  $statusCase */
        $statusCase = DomainStatusEnum::getCase($status);
        $status = is_null($statusCase) ? $status : $statusCase->translate();


        return [
            $this->cellStyleLeft($this[TableEnum::Name->dbName()]),
            $this->cellStyleCenter($status),
            $this->cellStyleLeft($this->domainCategory[DomainCategoriesTableEnum::Name->dbName()]),
            $this->cellStyleLeft($this->domainHolderAccount->domainHolder[DomainHoldersTableEnum::Name->dbName()]),
            $this->cellStyleLeft($this->domainHolderAccount[DomainHolderAccountsTableEnum::Username->dbName()]),
            $this->cellStyleCenter($this[TableEnum::AutoRenew->dbName()] ? "Yes" : "No"),
            $this->cellStyleCenter($this[TableEnum::Public->dbName()] ? "Yes" : "No"),
            $this->cellStyleCenter($this[TableEnum::Suspicious->dbName()] ? "Yes" : "No"),
            $this->cellStyleCenter($this[TableEnum::Reported->dbName()] ? "Yes" : "No"),
            $this->cellStyleLeft($user->convertUTCToLocalTime($this[TableEnum::RegisteredAt->dbName()])),
            $this->cellStyleLeft($user->convertUTCToLocalTime($this[TableEnum::ExpiresAt->dbName()])),
            $this->cellStyleLeft($user->convertUTCToLocalTime($this[TableEnum::AnnouncedAt->dbName()])),
            $this->cellStyleLeft($user->convertUTCToLocalTime($this[TableEnum::BlockedAt->dbName()])),
            $this->cellStyleLeft($this[TableEnum::Descr->dbName()]),

        ];
    }
}
