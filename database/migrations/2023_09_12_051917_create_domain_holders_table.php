<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DomainHoldersTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::DomainHolders->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::Name->dbName())->unique('domain_holders_name_unique');
            $table->longText(TableEnum::Url->dbName())->comment('URL to access holder site.');
            $table->boolean(TableEnum::IsActive->dbName())->default(1);
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_09_12_051917_create_domain_holders_table.php
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(DatabaseTablesEnum::DomainHolders->tableName());
    }

};
