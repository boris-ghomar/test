<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\CurrencyRatesTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::CurrencyRates->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::NameIso->dbName(), 3)->unique();
            $table->unsignedDecimal(TableEnum::OneUsdRate->dbName(), 24, 4)->nullable();
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_01_28_170554_create_currency_rates_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::CurrencyRates->tableName());
    }
};
