<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ReferralRewardPayments->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->comment('Referrer user id. This ID comes from users table.')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::RewardConclusionsId->dbName())->comment('This ID comes from referral_reward_conclusions table')->constrained(DatabaseTablesEnum::ReferralRewardConclusions->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::RewardItemId->dbName())->comment('This ID comes from referral_reward_items table.')->constrained(DatabaseTablesEnum::ReferralRewardItems->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedDecimal(TableEnum::Amount->dbName(), 24, 4);
            $table->boolean(TableEnum::IsSuccessful->dbName())->default(0)->comment('When the payment is successfully made on the partner side, the value changes to one.');
            $table->boolean(TableEnum::IsDone->dbName())->default(0)->comment('When the payment is completed, whether it is successful or not.');
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->text(TableEnum::SystemMessage->dbName())->nullable();
            $table->timestamp(TableEnum::QueuedAt->dbName())->nullable();
            $table->timestamps();

            $table->unique([TableEnum::UserId->dbName(), TableEnum::RewardConclusionsId->dbName(), TableEnum::RewardItemId->dbName()], 'referral_reward_payments_unique_row');
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_03_05_043655_create_referral_reward_payments_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ReferralRewardPayments->tableName());
    }
};
