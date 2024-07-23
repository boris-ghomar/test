<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Enums\Domains\DomainStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(DatabaseTablesEnum::Domains->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::Name->dbName())->unique();
            $table->foreignId(TableEnum::DomainCategoryId->dbName())->comment('Id from "domain_categories" table')->constrained(DatabaseTablesEnum::DomainCategories->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::DomainHolderAccountId->dbName())->comment('Id from "domain_holder_accounts" table')->constrained(DatabaseTablesEnum::DomainHolderAccounts->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean(TableEnum::AutoRenew->dbName())->default(1);
            $table->enum(TableEnum::Status->dbName(), DomainStatusEnum::names())->default(DomainStatusEnum::Unknown->name);
            $table->boolean(TableEnum::Public->dbName())->default(0)->comment('Public domains are domains that are distributed without considering the trust score.');
            $table->boolean(TableEnum::Suspicious->dbName())->default(0)->comment('Suspicious domains are domains that are distributed to suspicious clients.');
            $table->boolean(TableEnum::Reported->dbName())->default(0);
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamp(TableEnum::RegisteredAt->dbName())->nullable();
            $table->timestamp(TableEnum::ExpiresAt->dbName())->nullable();
            $table->timestamp(TableEnum::AnnouncedAt->dbName())->nullable();
            $table->timestamp(TableEnum::BlockedAt->dbName())->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_09_12_091357_create_domains_table.php
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(DatabaseTablesEnum::Domains->tableName());
    }
};
