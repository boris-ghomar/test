<?php

use App\Enums\Tickets\TicketsStatusEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\TicketsTableEnum as TableEnum;
use App\Enums\Tickets\TicketPrioritiesEnum;
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

        Schema::create(DatabaseTablesEnum::Tickets->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::OwnerId->dbName())->comment('The ID of the user who owns the ticket. This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::ResponderId->dbName())->nullable()->comment('Responder personnel ID. This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->string(TableEnum::TicketableType->dbName());
            $table->unsignedBigInteger(TableEnum::TicketableId->dbName());
            $table->text(TableEnum::Subject->dbName())->nullable();
            $table->enum(TableEnum::Priority->dbName(), TicketPrioritiesEnum::names())->default(TicketPrioritiesEnum::Normal->name);
            $table->enum(TableEnum::Status->dbName(), TicketsStatusEnum::names())->default(TicketsStatusEnum::New->name);
            $table->text(TableEnum::PrivateNote->dbName())->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_08_20_070804_create_tickets_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Tickets->tableName());
    }
};
