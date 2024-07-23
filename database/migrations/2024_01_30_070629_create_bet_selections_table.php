<?php

use App\Enums\Bets\BetSelectionStatusEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\BetSelectionsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::BetSelections->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::BetId->dbName())->comment('This ID comes from bets table')->constrained(DatabaseTablesEnum::Bets->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger(TableEnum::SelectionId->dbName())->nullable()->comment('Partner selection ID');
            $table->string(TableEnum::SelectionName->dbName())->nullable()->comment('Partner selection name');
            $table->unsignedBigInteger(TableEnum::MarketId->dbName())->nullable()->comment('Partner market ID');
            $table->string(TableEnum::MarketName->dbName())->nullable()->comment('Partner market name');
            $table->unsignedBigInteger(TableEnum::MatchId->dbName())->nullable()->comment('Partner match ID');
            $table->unsignedBigInteger(TableEnum::MatchShortId->dbName())->nullable()->comment('Partner match short ID');
            $table->string(TableEnum::MatchName->dbName())->nullable()->comment('Partner match name');
            $table->unsignedBigInteger(TableEnum::RegionId->dbName())->nullable()->comment('Partner region ID');
            $table->string(TableEnum::RegionName->dbName())->nullable()->comment('Partner region name');
            $table->unsignedBigInteger(TableEnum::CompetitionId->dbName())->nullable()->comment('Partner competition ID');
            $table->string(TableEnum::CompetitionName->dbName())->nullable()->comment('Partner competition name');
            $table->unsignedBigInteger(TableEnum::SportId->dbName())->nullable()->comment('Partner sport ID');
            $table->string(TableEnum::SportName->dbName())->nullable()->comment('Partner sport name');
            $table->string(TableEnum::SportAlias->dbName())->nullable()->comment('Partner sport alias name');
            $table->unsignedDecimal(TableEnum::Odds->dbName(), 12, 4);
            $table->decimal(TableEnum::Basis->dbName(), 12, 4)->nullable();
            $table->boolean(TableEnum::IsLive->dbName())->default(0);
            $table->text(TableEnum::MatchInfo->dbName())->nullable();
            $table->string(TableEnum::SelectionScore->dbName())->nullable();
            $table->boolean(TableEnum::IsOutright->dbName())->default(0);
            $table->text(TableEnum::ResettlementReason->dbName())->nullable();
            $table->enum(TableEnum::Status->dbName(), BetSelectionStatusEnum::names())->nullable();
            $table->timestamp(TableEnum::MatchStartDate->dbName())->nullable();
            $table->timestamps();

            $table->unique([TableEnum::BetId->dbName(), TableEnum::SelectionId->dbName()], 'bet_selections_row_unique');
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_01_30_070629_create_bet_selections_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::BetSelections->tableName());
    }
};
