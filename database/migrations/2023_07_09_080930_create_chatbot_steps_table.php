<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ChatbotStepsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ChatbotSteps->tableName(), function (Blueprint $table) {

            $table->id();
            $table->foreignId(TableEnum::ChatbotId->dbName())->index(TableEnum::ChatbotId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::Chatbots->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->bigInteger(TableEnum::ParentId->dbName())->default(0)->comment('This ID for steps without a parent is zero.');
            $table->string(TableEnum::Type->dbName());
            $table->string(TableEnum::Name->dbName())->nullable();
            $table->json(TableEnum::Action->dbName())->nullable();
            $table->integer(TableEnum::Position->dbName())->default(1);
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_07_09_080930_create_chatbot_steps_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ChatbotSteps->tableName());
    }
};
