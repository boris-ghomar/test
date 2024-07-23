<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum CurrencyRatesTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case NameIso;
    case OneUsdRate;
    case Descr;

    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        return match ($this) {

            default => (string) $value
        };
    }
}
