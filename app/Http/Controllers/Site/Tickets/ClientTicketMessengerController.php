<?php

namespace App\Http\Controllers\Site\Tickets;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\TicketsTableEnum;
use App\Enums\Tickets\TicketsStatusEnum;
use App\Http\Controllers\BackOffice\Tickets\TicketMessengerController;
use App\Models\Site\Tickets\MyTicket;

class ClientTicketMessengerController extends TicketMessengerController
{

    /**
     * Display a listing of the resource.
     */
    public function indexClient(MyTicket $myTicket)
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, MyTicket::class);

        $payload = $this->makePayload($myTicket);

        if (!is_string($payload))
            return $payload;

        $data = [
            self::PAYLOAD_KEY => $payload,
        ];

        return view('hhh.Site.pages.Tickets.TicketMessenger.index', $data);
    }

    /**
     * Check if user can send message
     *
     * @return bool
     */
    protected function canUserSendMessage(): bool
    {
        $statusCol = TicketsTableEnum::Status->dbName();

        if ($this->ticket->$statusCol === TicketsStatusEnum::WaitingForClient->name) return true;
        if ($this->ticket->$statusCol === TicketsStatusEnum::ClientReplied->name) return true;

        return false;
    }

    /**
     * Check if user can view messenger
     *
     * @return bool
     */
    protected function canUserViewMessenger(): bool
    {
        return $this->user->can(PermissionAbilityEnum::viewAny->name, MyTicket::class);
    }
}
