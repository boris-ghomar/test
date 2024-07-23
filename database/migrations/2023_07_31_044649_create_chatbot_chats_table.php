<?php

use App\Enums\Chatbot\Messenger\ChatbotChatStatusEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ChatbotChatsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ChatbotChats->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->nullable()->index(TableEnum::UserId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This ID comes from users table');
            $table->foreignId(TableEnum::ChatbotId->dbName())->index(TableEnum::ChatbotId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::Chatbots->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This ID comes from chatbots table');
            $table->enum(TableEnum::Status->dbName(), ChatbotChatStatusEnum::names())->default(ChatbotChatStatusEnum::Active->name);
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_07_31_044649_create_chatbot_chats_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ChatbotChats->tableName());
    }
};
