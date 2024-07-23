<?php

namespace App\Console\Commands\Chatbot;

use App\Enums\Database\Tables\ChatbotMessagesTableEnum;
use App\Enums\Database\Tables\TicketMessagesTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\Models\BackOffice\Chatbot\ChatbotMessage;
use App\Models\BackOffice\Tickets\TicketMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DeleteUnusedChatbotUserInputImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:Delete-Unused-Chatbot-UserInput-Images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unsed chatbot UserInput images that do not exist in the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileAssistant = new FileAssistant(ImageConfigEnum::ChatbotUserInputImage);

        $images = $fileAssistant->files();

        foreach ($images as $image) {

            $name = Str::of($image)->afterLast("/")->toString();

            $isExistsInChatbotMessages = ChatbotMessage::where(ChatbotMessagesTableEnum::Content->dbName(), 'like', '%' . $name . '%')->exists();
            $isExistsInTicketMessages = TicketMessage::where(TicketMessagesTableEnum::Content->dbName(), 'like', '%' . $name . '%')->exists();

            if (!$isExistsInChatbotMessages && !$isExistsInTicketMessages)
                $fileAssistant->deleteFile($image);
        }
    }
}
