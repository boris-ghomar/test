<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::AssignedDomains->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->comment('This ID comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::DomainId->dbName())->comment('Id from "domains" table')->constrained(DatabaseTablesEnum::Domains->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer(TableEnum::ClientTrustScore->dbName())->comment('Score at the time of domain allocation');
            $table->integer(TableEnum::DomainSuspiciousScore->dbName())->default(0)->comment('Score at the time of domain allocation');
            $table->boolean(TableEnum::Reported->dbName())->default(0);
            $table->timestamp(TableEnum::ReportedAt->dbName())->nullable();
            $table->boolean(TableEnum::FakeAssigned->dbName())->default(0)->comment('This field shows that this domain has already been blocked and fakely assigned to an invalid user.');
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_10_05_083723_create_assigned_domains_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::AssignedDomains->tableName());
    }
};
