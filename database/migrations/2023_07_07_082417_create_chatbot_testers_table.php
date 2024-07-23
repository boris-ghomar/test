<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ChatbotTestersTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ChatbotTesters->tableName(), function (Blueprint $table) {

            $table->uuid(TableEnum::Id->dbName())->primary();
            $table->foreignId(TableEnum::UserId->dbName())->unique(TableEnum::UserId->dbName() . '_unique')->index(TableEnum::UserId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::ChatbotId->dbName())->index(TableEnum::ChatbotId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::Chatbots->tableName())->cascadeOnUpdate()->cascadeOnDelete();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_07_07_082417_create_chatbot_testers_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ChatbotTesters->tableName());
    }
};
