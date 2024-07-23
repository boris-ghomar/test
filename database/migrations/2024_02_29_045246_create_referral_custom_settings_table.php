<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralCustomSettingsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ReferralCustomSettings->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->unique()->comment('This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::PackageId->dbName())->comment('This ID comes from referral_reward_packages table')->constrained(DatabaseTablesEnum::ReferralRewardPackages->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->text(TableEnum::PrivateNote->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_02_29_045246_create_referral_custom_settings_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ReferralCustomSettings->tableName());
    }
};
