<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\NotificationsTableEnum;
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
        Schema::create(DatabaseTablesEnum::Notifications->tableName(), function (Blueprint $table) {

            $table->uuid(NotificationsTableEnum::Id->dbName())->primary();
            $table->string(NotificationsTableEnum::Type->dbName());
            $table->morphs('notifiable');
            $table->text(NotificationsTableEnum::Data->dbName());
            $table->timestamp(NotificationsTableEnum::ReadAt->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_05_04_185131_create_notifications_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Notifications->tableName());
    }
};
