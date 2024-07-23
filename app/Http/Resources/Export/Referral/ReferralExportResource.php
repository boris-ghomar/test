<?php

namespace App\Http\Resources\Export\Referral;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ReferralsTableEnum as TableEnum;
use App\HHH_Library\Export\Traits\FormatExcelColumns;
use App\Http\Resources\ApiResponseResource;

class ReferralExportResource extends ApiResponseResource
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
            $this->cellStyleCenter($this[TableEnum::UserId->dbName()]),
            $this->cellStyleCenter($this->referred_bc_id),
            $this->cellStyleCenter($this->referred_username),

            $this->cellStyleCenter($this[TableEnum::ReferredBy->dbName()]),
            $this->cellStyleCenter($this->referrer_bc_id),
            $this->cellStyleCenter($this->referrer_username),

            $this->cellStyleCenter($this[TimestampsEnum::CreatedAt->dbName()]),
        ];
    }
}
