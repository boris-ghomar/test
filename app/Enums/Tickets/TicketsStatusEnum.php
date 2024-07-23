<?php

namespace App\Enums\Tickets;

use App\Enums\Database\Tables\TicketsTableEnum;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;
use App\Models\BackOffice\Tickets\Ticket;
use App\Notifications\Site\Tickets\YourTicketClosedNotification;
use App\Notifications\Site\Tickets\YourTicketWaitingYourResponseNotification;

enum TicketsStatusEnum implements Translatable
{
    use EnumActions;

    case ClientReplied;
    case New;
    case InProgress;
    case WaitingForClient;
    case Closed;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Site.TicketsStatusEnum.' . $this->name, [], is_null($locale) ? null : $locale->value);
    }

    /**
     * Get badge class for display in my chats list
     *
     * @return badge-info
     */
    public function getbadge(): string
    {

        return match ($this) {
            self::ClientReplied     => "badge-info",
            self::New               => "badge-warning",
            self::InProgress        => "badge-primary",
            self::WaitingForClient  => "badge-danger",
            self::Closed            => "badge-success",
        };
    }

    /**
     * Notify ticket owner of ticket status
     *
     * @param  mixed $ticket
     * @return void
     */
    public static function notifyTicketStatus(Ticket $ticket): void
    {
        try {

            $statusCol = TicketsTableEnum::Status->dbName();

            /** @var User $ticketOwner */
            $ticketOwner = $ticket->owner;

            switch ($ticket->$statusCol) {

                case self::WaitingForClient->name:
                    $ticketOwner->notify(new YourTicketWaitingYourResponseNotification($ticket->id));
                    break;
                case self::Closed->name:
                    $ticketOwner->notify(new YourTicketClosedNotification($ticket->id));
                    break;
            }
        } catch (\Throwable $th) {
        }
    }
}
