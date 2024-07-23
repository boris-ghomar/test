<?php

namespace App\Http\Resources\BackOffice\Domains;

use App\Enums\Database\Tables\DomainHolderAccountsTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;
use App\Models\User;

class DomainResource extends ApiResponseResource
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

        $domainHolderId = $this[DomainHolderAccountsTableEnum::DomainHolderId->dbName()];
        if (empty($domainHolderId)) {
            // Data comes from contoller
            $domainHolderId = $this->domainHolderAccount->domainHolder->id;
        }

        return [
            TableEnum::Id->dbName()                     => (int) $this[TableEnum::Id->dbName()],
            TableEnum::Name->dbName()                   => $this[TableEnum::Name->dbName()],
            TableEnum::DomainCategoryId->dbName()       => (int) $this[TableEnum::DomainCategoryId->dbName()],
            TableEnum::DomainHolderAccountId->dbName()  => (int) $this[TableEnum::DomainHolderAccountId->dbName()],
            TableEnum::AutoRenew->dbName()              => (bool) $this[TableEnum::AutoRenew->dbName()],
            TableEnum::RegisteredAt->dbName()           => $user->convertUTCToLocalTime($this[TableEnum::RegisteredAt->dbName()]),
            TableEnum::ExpiresAt->dbName()              => $user->convertUTCToLocalTime($this[TableEnum::ExpiresAt->dbName()]),
            TableEnum::AnnouncedAt->dbName()            => $user->convertUTCToLocalTime($this[TableEnum::AnnouncedAt->dbName()]),
            TableEnum::BlockedAt->dbName()              => $user->convertUTCToLocalTime($this[TableEnum::BlockedAt->dbName()]),
            TableEnum::Status->dbName()                 => $this[TableEnum::Status->dbName()],
            TableEnum::Public->dbName()                 => (bool) $this[TableEnum::Public->dbName()],
            TableEnum::Suspicious->dbName()             => (bool) $this[TableEnum::Suspicious->dbName()],
            TableEnum::Reported->dbName()               => (bool) $this[TableEnum::Reported->dbName()],
            TableEnum::Descr->dbName()                  => $this[TableEnum::Descr->dbName()],

            DomainHolderAccountsTableEnum::DomainHolderId->dbName()  => (int) $domainHolderId,
        ];
    }
}
