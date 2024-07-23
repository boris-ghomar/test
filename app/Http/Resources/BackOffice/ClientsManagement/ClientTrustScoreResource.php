<?php

namespace App\Http\Resources\BackOffice\ClientsManagement;

use App\Enums\Database\Tables\ClientTrustScoresTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Http\Resources\ApiResponseResource;

class ClientTrustScoreResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            TableEnum::Id->dbName()                 => (int) $this[TableEnum::Id->dbName()],
            TableEnum::UserId->dbName()             => (int) $this[TableEnum::UserId->dbName()],
            TableEnum::Score->dbName()              => (int) $this[TableEnum::Score->dbName()],
            TableEnum::DomainSuspicious->dbName()   => (int) $this[TableEnum::DomainSuspicious->dbName()],
            TableEnum::DepositCount->dbName()       => number_format($this[TableEnum::DepositCount->dbName()], 0),
            TableEnum::Balance->dbName()            => number_format($this[TableEnum::Balance->dbName()], 2),
            TableEnum::Descr->dbName()              => $this[TableEnum::Descr->dbName()],

            UsersTableEnum::RoleId->dbName()        => (int) $this[UsersTableEnum::RoleId->dbName()],

            ClientModelEnum::CurrencyId->dbName()   => $this[ClientModelEnum::CurrencyId->dbName()],
            'username'                              => $this->username,
            'betconstruct_id'                       => $this->betconstruct_id,
        ];
    }
}
