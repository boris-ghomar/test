<?php

namespace App\Http\Resources\BackOffice\Referral;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ReferralsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class ReferralResource extends ApiResponseResource
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
            TableEnum::Id->dbName()         => (int) $this[TableEnum::Id->dbName()],
            TableEnum::UserId->dbName()     => (int) $this[TableEnum::UserId->dbName()],
            TableEnum::ReferredBy->dbName() => $this[TableEnum::ReferredBy->dbName()],

            TimestampsEnum::CreatedAt->dbName() => $this[TimestampsEnum::CreatedAt->dbName()],

            'referred_username' => $this->referred_username,
            'referred_bc_id' => $this->referred_bc_id,

            'referrer_username' => $this->referrer_username,
            'referrer_bc_id' => $this->referrer_bc_id,
        ];
    }
}
