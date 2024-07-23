<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\LikesTableEnum as TableEnum;
use App\Enums\UserActions\LikableTypesEnum;
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
        Schema::create(DatabaseTablesEnum::Likes->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This ID comes from users table');
            $table->enum(TableEnum::LikableType->dbName(), LikableTypesEnum::names());
            $table->foreignId(TableEnum::LikableId->dbName());
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_06_13_153843_create_likes_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Likes->tableName());
    }
};
