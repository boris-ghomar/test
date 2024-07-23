<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DomainCategoriesTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::DomainCategories->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::Name->dbName())->unique('domain_categories_name_unique');
            $table->boolean(TableEnum::DomainAssignment->dbName())->default(0)->comment('If the value of this field is one, the domains of this category will be used by the system to assign domains to clients.');
            $table->boolean(TableEnum::IsActive->dbName())->default(1);
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_09_12_055750_create_domain_categories_table.php
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(DatabaseTablesEnum::DomainCategories->tableName());
    }
};
