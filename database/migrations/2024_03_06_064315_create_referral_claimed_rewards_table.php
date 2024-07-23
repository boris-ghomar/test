<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralClaimedRewardsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ReferralClaimedRewards->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->comment('Referrer user id. This ID comes from users table.')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::ReferralSessionId->dbName())->comment('This ID comes from referral_sessions table')->constrained(DatabaseTablesEnum::ReferralSessions->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::RewardItemId->dbName())->comment('This ID comes from referral_reward_items table.')->constrained(DatabaseTablesEnum::ReferralRewardItems->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->unique([TableEnum::UserId->dbName(), TableEnum::ReferralSessionId->dbName(), TableEnum::RewardItemId->dbName()], 'referral_claimed_rewards_unique_row');
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_03_06_064315_create_referral_claimed_rewards_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ReferralClaimedRewards->tableName());
    }
};
