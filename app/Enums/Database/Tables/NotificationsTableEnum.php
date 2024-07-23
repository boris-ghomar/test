<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum NotificationsTableEnum
{
    use EnumToDatabaseColumnName;

    /****** Defaults *******/
    case Id;
    case Type;
    case NotifiableType;
    case NotifiableId;
    case Data;
    case ReadAt;
    /****** Defaults END*******/

}
