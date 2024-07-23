<?php

namespace App\Http\Resources\BackOffice\Currencies;

use App\Enums\Database\Tables\CurrencyRatesTableEnum as TableEnum;
use App\Enums\General\CurrencyEnum;
use App\Http\Resources\ApiResponseResource;

class CurrencyRateResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var CurrencyEnum $currencyEnum */
        $currencyEnum = CurrencyEnum::getCase($this[TableEnum::NameIso->dbName()]);
        $name = is_null($currencyEnum) ? "" : $currencyEnum->translateWithoutIso();

        $oneUsdRate = $this[TableEnum::OneUsdRate->dbName()];
        $oneUsdRate = is_null($oneUsdRate) ? null : number_format($oneUsdRate, 2);

        return [
            TableEnum::Id->dbName()         => (int) $this[TableEnum::Id->dbName()],
            TableEnum::NameIso->dbName()    => $this[TableEnum::NameIso->dbName()],
            TableEnum::OneUsdRate->dbName() => $oneUsdRate,
            TableEnum::Descr->dbName()      => $this[TableEnum::Descr->dbName()],

            'name'      => $name,
        ];
    }
}
