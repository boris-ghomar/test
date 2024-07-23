<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\UserSettingsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::UserSettings->tableName(), function (Blueprint $table) {
            $table->uuid(TableEnum::Id->dbName())->primary();
            $table->foreignId(TableEnum::UserId->dbName())->index(TableEnum::UserId->dbName().'_foreign')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This id comes from users table');
            $table->foreignId(TableEnum::SettingId->dbName())->index(TableEnum::SettingId->dbName().'_foreign')->constrained(DatabaseTablesEnum::Settings->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This id comes from settings table');
            $table->text(TableEnum::Value->dbName())->nullable()->comment('value of the setting.');
            $table->timestamps();

            $table->unique([TableEnum::UserId->dbName(), TableEnum::SettingId->dbName()], 'user_settings_unique');
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_05_14_130839_create_user_settings_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::UserSettings->tableName());
    }
};
