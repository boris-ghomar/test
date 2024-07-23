<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum ChatbotStepsTableEnum
{
    use EnumToDatabaseColumnName;

    case Id;
    case ChatbotId;
    case ParentId;
    case Type;
    case Name;
    case Action;
    case Position;

        // Accessors
    case TranslatedStepType;
}
