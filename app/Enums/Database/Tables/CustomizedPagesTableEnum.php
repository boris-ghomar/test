<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum CustomizedPagesTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case UserId;
    case Route;
    case SelectedColumns;

    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        return match ($this) {

                //

            default => (string) $value
        };
    }
}
