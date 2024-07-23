<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum UsersTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    /****** Defaults *******/
    case Id;
    case Username;
    case Email;
    case EmailVerifiedAt;
    case Password;
    case RememberToken;

    case CurrentTeamId;
    case ProfilePhotoName;
    /****** Defaults END*******/

    /****** HHH *******/
    case Type;  // App\Enums\Users\UsersTypesEnum
    case RoleId; // foreignId from roles table
    case Status; //  App\HHH_Library\general\php\traits\Enums\EnumActions\UsersStatusEnum : Active|Suspended
    /****** HHH END *******/

        // Model accessors
    case PhotoFullName;
    case PhotoUrl;
    case DisplayName;
    case IsEmailVerified;

    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        return match ($this) {

            self::Id => (int) $value,

            default => (string) $value
        };
    }
}
