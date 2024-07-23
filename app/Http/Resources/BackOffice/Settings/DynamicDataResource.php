<?php

namespace App\Http\Resources\BackOffice\Settings;

use App\Enums\Database\Tables\DynamicDatasTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class DynamicDataResource extends ApiResponseResource
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
            TableEnum::VarName->dbName()    => $this[TableEnum::VarName->dbName()],
            TableEnum::VarValue->dbName()   => $this[TableEnum::VarValue->dbName()],
            TableEnum::Descr->dbName()      => $this[TableEnum::Descr->dbName()],
        ];
    }
}
