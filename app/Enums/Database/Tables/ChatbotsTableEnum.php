<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum ChatbotsTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case Name;
    case IsActive;
    case ProfilePhotoName;
    case Descr;

        // Model accessors
    case PhotoUrl;


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
