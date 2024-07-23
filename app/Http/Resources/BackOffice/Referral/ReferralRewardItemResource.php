<?php

namespace App\Http\Resources\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralRewardItemsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class ReferralRewardItemResource extends ApiResponseResource
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
            TableEnum::PackageId->dbName()          => (int) $this[TableEnum::PackageId->dbName()],
            TableEnum::Name->dbName()               => $this[TableEnum::Name->dbName()],
            TableEnum::DisplayName->dbName()        => $this[TableEnum::DisplayName->dbName()],
            TableEnum::Type->dbName()               => $this[TableEnum::Type->dbName()],
            TableEnum::BonusId->dbName()            => $this[TableEnum::BonusId->dbName()],
            TableEnum::Percentage->dbName()         => number_format($this[TableEnum::Percentage->dbName()], 2),
            TableEnum::IsActive->dbName()           => (bool) $this[TableEnum::IsActive->dbName()],
            TableEnum::DisplayPriority->dbName()    => (int) $this[TableEnum::DisplayPriority->dbName()],
            TableEnum::PaymentPriority->dbName()    => (int) $this[TableEnum::PaymentPriority->dbName()],
            TableEnum::PrivateNote->dbName()        => $this[TableEnum::PrivateNote->dbName()],
        ];
    }
}
