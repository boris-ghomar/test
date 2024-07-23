<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\SettingsTableEnum;
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
        Schema::create(DatabaseTablesEnum::Settings->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(SettingsTableEnum::Name->dbName())->unique('settings_name_unique')->comment("unique name string used as a key for settings.");
            $table->text(SettingsTableEnum::Value->dbName())->nullable()->comment("value of the setting.");
            $table->string(SettingsTableEnum::Cast->dbName(), 20)->comment("cast type will be used to cast the value to string, integer or boolean etc.");
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_05_08_055844_create_settings_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Settings->tableName());
    }
};
