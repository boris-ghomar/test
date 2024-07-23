<?php

namespace App\Console\Commands\Chatbot;

use App\Enums\Chatbot\Messenger\ChatbotChatStatusEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ChatbotChatsTableEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Models\BackOffice\Chatbot\ChatbotChat;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredChatbotChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:delete-expired-chatbot-chats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired chatbot chats.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expireDate = Carbon::now()->subDays(AppSettingsEnum::ChatbotClosedChatsDaysOfKeeping->getValue());

        ChatbotChat::where(ChatbotChatsTableEnum::Status->dbName(), ChatbotChatStatusEnum::Closed->name)
            ->where(TimestampsEnum::UpdatedAt->dbName(), '<', $expireDate)
            ->delete();
    }
}
