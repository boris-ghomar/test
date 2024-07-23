<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum as TableEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
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
        Schema::create(DatabaseTablesEnum::ReferralSessions->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::Name->dbName())->unique();
            $table->foreignId(TableEnum::PackageId->dbName())->comment('This ID comes from referral_reward_packages table')->constrained(DatabaseTablesEnum::ReferralRewardPackages->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum(TableEnum::Status->dbName(), ReferralSessionStatusEnum::names())->default(ReferralSessionStatusEnum::Upcoming->name);
            $table->timestamp(TableEnum::StartedAt->dbName())->nullable();
            $table->timestamp(TableEnum::FinishedAt->dbName())->nullable();
            $table->text(TableEnum::PrivateNote->dbName())->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_03_01_044711_create_referral_sessions_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ReferralSessions->tableName());
    }
};
