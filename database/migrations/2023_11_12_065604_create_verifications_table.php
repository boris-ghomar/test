<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\VerificationsTableEnum as TableEnum;
use App\Enums\Users\VerificationTypesEnum;
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
        Schema::create(DatabaseTablesEnum::Verifications->tableName(), function (Blueprint $table) {
            $table->uuid(TableEnum::Id->dbName())->primary();
            $table->foreignId(TableEnum::UserId->dbName())->nullable()->comment('This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum(TableEnum::Type->dbName(), VerificationTypesEnum::names())->default(VerificationTypesEnum::Email->name);
            $table->string(TableEnum::OldValue->dbName())->nullable();
            $table->string(TableEnum::NewValue->dbName());
            $table->string(TableEnum::Code->dbName())->nullable();
            $table->text(TableEnum::Link->dbName())->nullable();
            $table->boolean(TableEnum::IsVerified->dbName())->default(0);
            $table->timestamps();
            $table->timestamp(TableEnum::ValidUntil->dbName())->nullable()->comment('This verification is valid and usable until this time.');
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_11_12_065604_create_verifications_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Verifications->tableName());
    }
};
