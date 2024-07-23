<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\UsersTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::Users->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string(TableEnum::Email->dbName())->unique('users_email_unique');
            $table->timestamp(TableEnum::EmailVerifiedAt->dbName())->nullable();
            $table->string(TableEnum::Password->dbName());
            $table->rememberToken();
            $table->foreignId(TableEnum::CurrentTeamId->dbName())->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2014_10_12_000000_create_users_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Users->tableName());
    }
};
