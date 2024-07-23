<?php

namespace App\Console\Commands\Tickets;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\TicketsTableEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Enums\Tickets\TicketsStatusEnum;
use App\Models\BackOffice\Tickets\Ticket;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:delete-expired-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired tickets.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expireDate = Carbon::now()->subDays(AppSettingsEnum::TicketClosedTicketsDaysOfKeeping->getValue());

        Ticket::where(TicketsTableEnum::Status->dbName(), TicketsStatusEnum::Closed->name)
            ->where(TimestampsEnum::UpdatedAt->dbName(), '<', $expireDate)
            ->withTrashed()
            ->forceDelete();
    }
}
