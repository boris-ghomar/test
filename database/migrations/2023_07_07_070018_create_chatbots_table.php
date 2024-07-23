<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ChatbotsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::Chatbots->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::Name->dbName())->unique(TableEnum::Name->dbName() . '_unique');
            $table->boolean(TableEnum::IsActive->dbName())->default(0);
            $table->string(TableEnum::ProfilePhotoName->dbName())->nullable();
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_07_07_070018_create_chatbots_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Chatbots->tableName());
    }
};
