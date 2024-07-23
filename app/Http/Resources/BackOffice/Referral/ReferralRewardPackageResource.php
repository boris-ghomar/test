<?php

namespace App\Http\Resources\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class ReferralRewardPackageResource extends ApiResponseResource
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
            TableEnum::Name->dbName()           => $this[TableEnum::Name->dbName()],
            TableEnum::DisplayName->dbName()    => $this[TableEnum::DisplayName->dbName()],
            TableEnum::ClaimCount->dbName()     => (int) $this[TableEnum::ClaimCount->dbName()],
            TableEnum::Descr->dbName()          => $this[TableEnum::Descr->dbName()],
            TableEnum::IsActive->dbName()       => (bool) $this[TableEnum::IsActive->dbName()],

            TableEnum::MinBetCountReferrer->dbName()        => (int) $this[TableEnum::MinBetCountReferrer->dbName()],
            TableEnum::MinBetOddsReferrer->dbName()         => number_format($this[TableEnum::MinBetOddsReferrer->dbName()], 2),
            TableEnum::MinBetAmountUsdReferrer->dbName()    => number_format($this[TableEnum::MinBetAmountUsdReferrer->dbName()], 2),
            TableEnum::MinBetAmountIrrReferrer->dbName()    => number_format($this[TableEnum::MinBetAmountIrrReferrer->dbName()], 2),

            TableEnum::MinBetCountReferred->dbName()        => (int) $this[TableEnum::MinBetCountReferred->dbName()],
            TableEnum::MinBetOddsReferred->dbName()         => number_format($this[TableEnum::MinBetOddsReferred->dbName()], 2),
            TableEnum::MinBetAmountUsdReferred->dbName()    => number_format($this[TableEnum::MinBetAmountUsdReferred->dbName()], 2),
            TableEnum::MinBetAmountIrrReferred->dbName()    => number_format($this[TableEnum::MinBetAmountIrrReferred->dbName()], 2),

            TableEnum::PrivateNote->dbName()    => $this[TableEnum::PrivateNote->dbName()],
        ];
    }
}
