<?php

namespace App\Http\Resources\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralCustomSettingsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class ReferralCustomSettingResource extends ApiResponseResource
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
            TableEnum::PackageId->dbName()          => (int) $this[TableEnum::PackageId->dbName()],
            TableEnum::PrivateNote->dbName()        => $this[TableEnum::PrivateNote->dbName()],

            'bc_username'                           => $this['bc_username'],
            'bc_id'                                 => $this['bc_id'],
        ];
    }
}
