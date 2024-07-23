<?php

namespace App\Enums\Chatbot\Messenger;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ChatbotMessageTypesEnum
{
    use EnumActions;

    case Text;
    case Image;
    case Button;
    case Input;
    case Filter;
    case BotAction;
}
