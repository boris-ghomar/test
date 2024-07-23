<?php

namespace App\Console\Commands\Chatbot;

use App\Enums\Chatbot\Messenger\ChatbotChatStatusEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ChatbotChatsTableEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Models\BackOffice\Chatbot\ChatbotChat;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseInactiveChatbotChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:close-inactive-chatbot-chats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close inactive chatbot chats.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $expireDate = Carbon::now()->subHours(AppSettingsEnum::ChatbotInactiveChatsExpirationHours->getValue());

        $statusCol = ChatbotChatsTableEnum::Status->dbName();
        $statusClosed = ChatbotChatStatusEnum::Closed->name;

        $inactiveChatbotChats = ChatbotChat::where(TimestampsEnum::UpdatedAt->dbName(), '<', $expireDate)
            ->where($statusCol, '!=', $statusClosed)
            ->get();

        foreach ($inactiveChatbotChats as $chatbotChat) {

            $chatbotChat[$statusCol] = $statusClosed;
            $chatbotChat->save();
        }
    }
}
