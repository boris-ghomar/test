<?php

namespace App\Enums\Tickets;


use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum TicketMessageTypesEnum
{
    use EnumActions;

    case Text;
    case TicketImage;
    case ChatbotImage;
    case File;
}
