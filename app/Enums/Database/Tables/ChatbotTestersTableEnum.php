<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum ChatbotTestersTableEnum
{
    use EnumToDatabaseColumnName;

    case Id;
    case ChatbotId;
    case UserId;
}
