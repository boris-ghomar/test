<?php

use App\Enums\Chatbot\Messenger\ChatbotMessageTypesEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ChatbotMessagesTableEnum as TableEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(DatabaseTablesEnum::ChatbotMessages->tableName(), function (Blueprint $table) {
            $table->uuid(TableEnum::Id->dbName())->primary();
            $table->foreignId(TableEnum::ChatbotChatId->dbName())->index(TableEnum::ChatbotChatId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::ChatbotChats->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This ID comes from chatbot_chats table');
            $table->foreignId(TableEnum::ChatbotStepId->dbName())->index(TableEnum::ChatbotStepId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::ChatbotSteps->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This ID comes from chatbot_steps table');
            $table->boolean(TableEnum::IsBotMessage->dbName());
            $table->enum(TableEnum::Type->dbName(), ChatbotMessageTypesEnum::names());
            $table->json(TableEnum::Content->dbName());
            $table->boolean(TableEnum::IsPassed->dbName());
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_07_31_045013_create_chatbot_messages_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ChatbotMessages->tableName());
    }
};
