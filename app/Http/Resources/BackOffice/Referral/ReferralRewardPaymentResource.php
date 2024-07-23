<?php

namespace App\Http\Resources\BackOffice\Referral;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum as TableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Http\Resources\ApiResponseResource;

class ReferralRewardPaymentResource extends ApiResponseResource
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
            TableEnum::Id->dbName()             => (int) $this[TableEnum::Id->dbName()],
            TableEnum::UserId->dbName()         => (int) $this[TableEnum::UserId->dbName()],
            TableEnum::Amount->dbName()         => number_format($this[TableEnum::Amount->dbName()], 2),
            TableEnum::IsDone->dbName()         => (bool) $this[TableEnum::IsDone->dbName()],
            TableEnum::IsSuccessful->dbName()   => (bool) $this[TableEnum::IsSuccessful->dbName()],
            TableEnum::SystemMessage->dbName()  => $this[TableEnum::SystemMessage->dbName()],
            TableEnum::Descr->dbName()          => $this[TableEnum::Descr->dbName()],
            TimestampsEnum::CreatedAt->dbName() => $this[TimestampsEnum::CreatedAt->dbName()],

            'bc_id'             => $this->bc_id,
            'bc_username'       => $this->bc_username,
            ClientModelEnum::CurrencyId->dbName() => $this[ClientModelEnum::CurrencyId->dbName()],

            'reward_name'       => $this->reward_name,

        ];
    }
}
