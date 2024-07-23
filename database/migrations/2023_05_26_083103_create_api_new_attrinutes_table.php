<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ApiNewAttrinutesTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::ApiNewAttrinutes->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(TableEnum::ClassName->dbName());
            $table->string(TableEnum::Attrinute->dbName());
            $table->json(TableEnum::Values->dbName());
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();

            $table->unique([TableEnum::ClassName->dbName(), TableEnum::Attrinute->dbName()], 'class_attribute_unique');
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_05_26_083103_create_api_new_attrinutes_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ApiNewAttrinutes->tableName());
    }
};
