<?php

namespace App\Http\Resources\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralSessionsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class ReferralSessionResource extends ApiResponseResource
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
            TableEnum::Id->dbName()                         => (int) $this[TableEnum::Id->dbName()],
            TableEnum::Name->dbName()                       => $this[TableEnum::Name->dbName()],
            TableEnum::PackageId->dbName()                  => (int) $this[TableEnum::PackageId->dbName()],
            TableEnum::Status->dbName()                     => $this[TableEnum::Status->dbName()],
            TableEnum::StartedAt->dbName()                  => $this[TableEnum::StartedAt->dbName()],
            TableEnum::FinishedAt->dbName()                 => $this[TableEnum::FinishedAt->dbName()],
            TableEnum::PrivateNote->dbName()                => $this[TableEnum::PrivateNote->dbName()],

        ];
    }
}
