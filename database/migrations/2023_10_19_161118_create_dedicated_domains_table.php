<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DedicatedDomainsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::DedicatedDomains->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::Name->dbName())->unique();
            $table->foreignId(TableEnum::DomainId->dbName())->comment('Id from "domains" table')->constrained(DatabaseTablesEnum::Domains->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();

            // php artisan migrate:refresh --path=/database/migrations/2023_10_19_161118_create_dedicated_domains_table.php
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::DedicatedDomains->tableName());
    }
};
