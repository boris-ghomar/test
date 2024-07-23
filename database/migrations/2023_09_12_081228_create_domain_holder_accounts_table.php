<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DomainHolderAccountsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::DomainHolderAccounts->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::DomainHolderId->dbName())->comment('Id from "domain_holders" table')->constrained(DatabaseTablesEnum::DomainHolders->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->string(TableEnum::Username->dbName());
            $table->string(TableEnum::Email->dbName())->nullable();
            $table->boolean(TableEnum::IsActive->dbName())->default(1);
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique([TableEnum::DomainHolderId->dbName(), TableEnum::Username->dbName()], 'unique_row');
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_09_12_081228_create_domain_holder_accounts_table.php
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(DatabaseTablesEnum::DomainHolderAccounts->tableName());
    }
};
