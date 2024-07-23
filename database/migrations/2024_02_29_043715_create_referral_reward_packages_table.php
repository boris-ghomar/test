<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ReferralRewardPackages->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::Name->dbName())->unique();
            $table->string(TableEnum::DisplayName->dbName());
            $table->unsignedTinyInteger(TableEnum::ClaimCount->dbName())->default(1);
            $table->text(TableEnum::Descr->dbName())->nullable()->comment('Description to display to the client');
            $table->boolean(TableEnum::IsActive->dbName())->default(0);

            $table->unsignedSmallInteger(TableEnum::MinBetCountReferrer->dbName())->default(0);
            $table->unsignedDecimal(TableEnum::MinBetOddsReferrer->dbName(), 12, 4)->default(1);
            $table->unsignedDecimal(TableEnum::MinBetAmountUsdReferrer->dbName(), 24, 4)->default(0);
            $table->unsignedDecimal(TableEnum::MinBetAmountIrrReferrer->dbName(), 24, 4)->default(0);

            $table->unsignedSmallInteger(TableEnum::MinBetCountReferred->dbName())->default(0);
            $table->unsignedDecimal(TableEnum::MinBetOddsReferred->dbName(), 12, 4)->default(1);
            $table->unsignedDecimal(TableEnum::MinBetAmountUsdReferred->dbName(), 24, 4)->default(0);
            $table->unsignedDecimal(TableEnum::MinBetAmountIrrReferred->dbName(), 24, 4)->default(0);

            $table->text(TableEnum::PrivateNote->dbName())->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_02_29_043715_create_referral_reward_packages_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ReferralRewardPackages->tableName());
    }
};
