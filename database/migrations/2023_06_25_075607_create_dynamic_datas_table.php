<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DynamicDatasTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::DynamicDatas->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::VarName->dbName())->unique(TableEnum::VarName->dbName() . '_unique');
            $table->text(TableEnum::VarValue->dbName())->nullable();
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_06_25_075607_create_dynamic_datas_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::DynamicDatas->tableName());
    }
};
