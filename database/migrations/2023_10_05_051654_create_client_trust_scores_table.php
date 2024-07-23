<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ClientTrustScoresTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ClientTrustScores->tableName(), function (Blueprint $table) {

            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->comment('This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer(TableEnum::Score->dbName())->nullable();
            $table->integer(TableEnum::DomainSuspicious->dbName())->default(0)->comment('The activity of users with a score above zero has been evaluated in the domains system.');
            $table->integer(TableEnum::DepositCount->dbName())->nullable();
            $table->float(TableEnum::Balance->dbName(), 22, 2)->nullable();
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_10_05_051654_create_client_trust_scores_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ClientTrustScores->tableName());
    }
};
