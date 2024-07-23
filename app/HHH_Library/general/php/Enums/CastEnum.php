<?php

namespace App\HHH_Library\general\php\Enums;

use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Castable;

enum CastEnum implements Castable
{
    use EnumActions;

    case String;
    case Int;
    case Float;
    case Boolean;

    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        if (is_array($value)) return $value;

        return match ($this) {
            self::String    => $value . "",
            self::Int       => intval($value),
            self::Float     => floatval($value),
            self::Boolean   => filter_var($value, FILTER_VALIDATE_BOOLEAN),

            default => $value
        };
    }
}
