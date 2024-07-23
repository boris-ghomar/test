<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralRewardConclusionsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ReferralRewardConclusions->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->comment('Referrer user id. This ID comes from users table.')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::ReferralSessionId->dbName())->comment('This ID comes from referral_sessions table')->constrained(DatabaseTablesEnum::ReferralSessions->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger(TableEnum::TotalEffectiveBetsCount->dbName())->nullable()->comment('Total number of effective bets of introduced users');
            $table->unsignedDecimal(TableEnum::TotalEffectiveBetsAmount->dbName(), 24, 4)->nullable()->comment('Total amounts of effective bets of introduced users(Based on user currency).');
            $table->unsignedSmallInteger(TableEnum::RewardsCount->dbName())->nullable();
            $table->boolean(TableEnum::IsDone->dbName())->default(0)->comment('When the all referral_reward_payments is done.');
            $table->timestamp(TableEnum::CalculatedUntil->dbName())->nullable();
            $table->timestamp(TableEnum::CalculatedAt->dbName())->nullable();
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();

            $table->unique([TableEnum::ReferralSessionId->dbName(), TableEnum::UserId->dbName()], 'referral_reward_conclusion_unique_row');
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_03_04_060635_create_referral_reward_conclusions_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ReferralRewardConclusions->tableName());
    }
};
