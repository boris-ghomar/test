<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum PersonnelExtrasTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case UserId; // foreignId from users table
    case FirstName;
    case LastName;
    case AliasName;
    case Gender; //  App\HHH_Library\general\php\Enums\GendersEnum : Male | Female
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
