<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\TicketMessagesTableEnum as TableEnum;
use App\Enums\Tickets\TicketMessageTypesEnum;
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
        Schema::create(DatabaseTablesEnum::TicketMessages->tableName(), function (Blueprint $table) {
            $table->uuid(TableEnum::Id->dbName())->primary();
            $table->foreignId(TableEnum::UserId->dbName())->comment('This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::TicketId->dbName())->comment('This ID comes from tickets table')->constrained(DatabaseTablesEnum::Tickets->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum(TableEnum::Type->dbName(), TicketMessageTypesEnum::names());
            $table->text(TableEnum::Content->dbName());
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_08_20_162014_create_ticket_messages_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::TicketMessages->tableName());
    }
};
