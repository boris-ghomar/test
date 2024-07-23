<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DomainExtensionsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::DomainExtensions->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::Name->dbName())->unique();
            $table->boolean(TableEnum::LimitedOrder->dbName())->default(0)->comment('For purchase in limited quantities.');
            $table->boolean(TableEnum::IsActive->dbName())->default(1);
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_09_12_051913_create_domain_extensions_table.php
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(DatabaseTablesEnum::DomainExtensions->tableName());
    }

};
