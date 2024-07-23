<?php

namespace App\Console\Commands\Tickets;

use App\Enums\Database\Tables\TicketMessagesTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\Models\BackOffice\Tickets\TicketMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DeleteUnusedTicketImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:Delete-Unused-Ticket-Images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unsed ticket images that do not exist in the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileAssistant = new FileAssistant(ImageConfigEnum::TicketMessage);

        $images = $fileAssistant->files();

        foreach ($images as $image) {

            $name = Str::of($image)->afterLast("/")->toString();

            if (!TicketMessage::where(TicketMessagesTableEnum::Content->dbName(), 'like', '%' . $name . '%')->exists()) {
                $fileAssistant->deleteFile($image);
            }
        }
    }
}
