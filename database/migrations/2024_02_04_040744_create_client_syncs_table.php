<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ClientSyncsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ClientSyncs->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->unique()->comment('This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamp(TableEnum::BetsSyncDate->dbName())->nullable()->comment('Date of last synchronization of bets history.');
            $table->timestamp(TableEnum::BetsSyncStartedAt->dbName())->nullable()->comment('Bets sync start time');
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_02_04_040744_create_client_syncs_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ClientSyncs->tableName());
    }
};
