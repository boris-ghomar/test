<?php

namespace App\Enums\Tickets;

use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Models\BackOffice\Chatbot\ChatbotStep;

enum TicketableTypesEnum
{
    use EnumActions;

    case ChatbotStep;

    /**
     * Get the ticketable model class
     *
     * @return mixed
     */
    public function getTicketableModelClass(): mixed
    {
        return match ($this) {

            self::ChatbotStep => ChatbotStep::class,

            default => null
        };
    }

}
