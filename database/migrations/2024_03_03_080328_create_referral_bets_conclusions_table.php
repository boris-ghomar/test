<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralBetsConclusionsTableEnum as TableEnum;
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
        /**
         * NOTE:
         * 1. This table stores the referred bets conclusions based on the referrer client currency.
         *
         * 2.  All amounts are stored based on the referrer client's currency so that
         *      there is no need to exchange each one when summarizing the amounts and
         *      making the final payment for referrer client.
         */

        Schema::create(DatabaseTablesEnum::ReferralBetsConclusions->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::ReferralSessionId->dbName())->comment('This ID comes from referral_sessions table')->constrained(DatabaseTablesEnum::ReferralSessions->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::ReferrerId->dbName())->comment('This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::ReferredId->dbName())->comment('This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger(TableEnum::BetsCount->dbName());
            $table->unsignedDecimal(TableEnum::BetsTotalAmount->dbName(), 24, 4)->comment('Based on Referrer currency');
            $table->timestamp(TableEnum::CalculatedUntil->dbName())->nullable();
            $table->timestamp(TableEnum::CalculatedAt->dbName())->nullable();
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();

            $table->unique([TableEnum::ReferralSessionId->dbName(), TableEnum::ReferredId->dbName()], 'referral_reward_unique_row');
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_03_03_080328_create_referral_bets_conclusions_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ReferralBetsConclusions->tableName());
    }
};
