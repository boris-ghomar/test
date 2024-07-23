<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PersonnelExtrasTableEnum;
use App\HHH_Library\general\php\Enums\GendersEnum;
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
        Schema::create(DatabaseTablesEnum::PersonnelExtras->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(PersonnelExtrasTableEnum::UserId->dbName())->unique()->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This id comes from users table');
            $table->string(PersonnelExtrasTableEnum::FirstName->dbName());
            $table->string(PersonnelExtrasTableEnum::LastName->dbName());
            $table->string(PersonnelExtrasTableEnum::AliasName->dbName())->nullable();
            $table->enum(PersonnelExtrasTableEnum::Gender->dbName(), GendersEnum::names());
            $table->string(PersonnelExtrasTableEnum::Descr->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_04_22_073341_create_personnel_extras_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::PersonnelExtras->tableName());
    }
};
