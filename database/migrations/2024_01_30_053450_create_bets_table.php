<?php

use App\Enums\Bets\BetAcceptTypeEnum;
use App\Enums\Bets\BetContextEnum;
use App\Enums\Bets\BetStatusEnum;
use App\Enums\Bets\BetTypeEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\BetsTableEnum as TableEnum;
use App\Enums\General\CurrencyEnum;
use App\Enums\General\PartnerEnum;
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
        Schema::create(DatabaseTablesEnum::Bets->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->comment('This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum(TableEnum::Partner->dbName(), PartnerEnum::names());
            $table->enum(TableEnum::Context->dbName(), BetContextEnum::names());
            $table->boolean(TableEnum::IsReferralBet->dbName())->comment('It is conditional whether it has the conditions to be included in the referral calculations or not.');

            $table->unsignedBigInteger(TableEnum::PartnerBetId->dbName())->comment('Partner bet ID');
            $table->enum(TableEnum::BetType->dbName(), BetTypeEnum::names())->nullable();
            $table->unsignedBigInteger(TableEnum::TransactionId->dbName())->nullable();
            $table->unsignedDecimal(TableEnum::Amount->dbName(), 24, 4);
            $table->unsignedDecimal(TableEnum::WinAmount->dbName(), 24, 4)->nullable();
            $table->unsignedDecimal(TableEnum::Odds->dbName(), 12, 4);
            $table->unsignedBigInteger(TableEnum::BonusId->dbName())->nullable();
            $table->unsignedDecimal(TableEnum::BonusBetAmount->dbName(), 24, 4)->nullable();
            $table->enum(TableEnum::Status->dbName(), BetStatusEnum::names())->nullable();
            $table->unsignedDecimal(TableEnum::CashoutAmount->dbName(), 24, 4)->nullable();
            $table->boolean(TableEnum::IsLive->dbName())->default(0);
            $table->enum(TableEnum::Currency->dbName(), CurrencyEnum::names())->nullable();
            $table->unsignedBigInteger(TableEnum::ExternalId->dbName())->nullable()->comment('Partner external ID');
            $table->unsignedBigInteger(TableEnum::Barcode->dbName())->nullable()->comment('The barcode printed on the betslip.');
            $table->unsignedBigInteger(TableEnum::ParentBetId->dbName())->nullable()->comment('In case of partial cashouts it will show main bet ID.');
            $table->enum(TableEnum::AcceptType->dbName(), BetAcceptTypeEnum::names())->nullable();
            $table->boolean(TableEnum::IsQueued->dbName())->default(0)->comment('App internal item. Whether the bet is queued for update or not.');
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamp(TableEnum::PlacedAt->dbName())->nullable();
            $table->timestamp(TableEnum::CalculatedAt->dbName())->nullable();
            $table->timestamp(TableEnum::PaidAt->dbName())->nullable();
            $table->timestamps();

            $table->unique([TableEnum::Partner->dbName(), TableEnum::PartnerBetId->dbName()], 'bets_partner_bet_id_unique');
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_01_30_053450_create_bets_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Bets->tableName());
    }
};
