<?php

namespace App\Http\Resources\Export\Referral;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum as TableEnum;
use App\HHH_Library\Export\Traits\FormatExcelColumns;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Http\Resources\ApiResponseResource;

class ReferralRewardPaymentExportResource extends ApiResponseResource
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
            $this->cellStyleCenter($this[TableEnum::Id->dbName()]),
            $this->cellStyleCenter($this[TableEnum::UserId->dbName()]),
            $this->cellStyleCenter($this->bc_id),
            $this->cellStyleCenter($this->bc_username),
            $this->cellStyleCenter($this->reward_name),
            $this->cellStyleCenter(number_format($this[TableEnum::Amount->dbName()], 2)),
            $this->cellStyleCenter($this[ClientModelEnum::CurrencyId->dbName()]),
            $this->cellStyleCenter($this[TableEnum::IsDone->dbName()] ? 'YES' : 'NO'),
            $this->cellStyleCenter($this[TableEnum::IsSuccessful->dbName()] ? 'YES' : 'NO'),
            $this->cellStyleCenter($this[TimestampsEnum::CreatedAt->dbName()]),
            $this->cellStyleCenter($this[TableEnum::SystemMessage->dbName()]),
            $this->cellStyleCenter($this[TableEnum::Descr->dbName()]),
        ];
    }
}
