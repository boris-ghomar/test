<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum ReferralSessionsTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case PackageId;
    case Name;
    case Status;
    case StartedAt;
    case FinishedAt;
    case PrivateNote;


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
