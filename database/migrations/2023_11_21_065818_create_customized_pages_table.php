<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\CustomizedPagesTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::CustomizedPages->tableName(), function (Blueprint $table) {
            $table->uuid(TableEnum::Id->dbName())->primary();
            $table->foreignId(TableEnum::UserId->dbName())->comment('This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->string(TableEnum::Route->dbName());
            $table->json(TableEnum::SelectedColumns->dbName())->nullable();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_11_21_065818_create_customized_pages_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::CustomizedPages->tableName());
    }
};
