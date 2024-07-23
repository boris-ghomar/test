<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum RolesTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case Name;
    case DisplayName;
    case Type;
    case IsActive;
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

            self::IsActive => CastEnum::Boolean->cast($value),

            default => (string) $value
        };
    }
}
