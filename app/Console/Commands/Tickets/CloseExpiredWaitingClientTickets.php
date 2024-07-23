<?php

namespace App\Console\Commands\Tickets;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\TicketsTableEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Enums\Tickets\TicketsStatusEnum;
use App\Models\BackOffice\Tickets\Ticket;
use App\Notifications\Site\Tickets\YourWaitingTicketExpiredNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseExpiredWaitingClientTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:close-expired-waiting-client-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close expired waiting client tickets.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $maxWaitingHours = AppSettingsEnum::TicketWaitingClientTicketsExpirationHours->getValue();
        $expireDate = Carbon::now()->subHours($maxWaitingHours);

        $statusCol = TicketsTableEnum::Status->dbName();
        $privateNoteCol = TicketsTableEnum::PrivateNote->dbName();

        $statusWaiting = TicketsStatusEnum::WaitingForClient->name;
        $statusClosed = TicketsStatusEnum::Closed->name;

        $unansweredTickets = Ticket::where(TimestampsEnum::UpdatedAt->dbName(), '<', $expireDate)
            ->where($statusCol, $statusWaiting)
            ->get();

        $closeNote = sprintf(
            "Ticket monitoring Bot:\nThis ticket was not answered by the client in %s hours and was automatically closed by the system.",
            $maxWaitingHours
        );

        foreach ($unansweredTickets as $ticket) {

            $ticket[$statusCol] = $statusClosed;

            $privateNote = $ticket->$privateNoteCol;
            $ticket[$privateNoteCol] = empty($privateNote) ? $closeNote : $privateNote . "\n" . $closeNote;

            $ticket->save();

            $ticket->owner->notify(new YourWaitingTicketExpiredNotification($ticket->id, $maxWaitingHours));
        }
    }
}
