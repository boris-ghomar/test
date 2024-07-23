<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ClientCategoryMapsTableEnum as TableEnum;
use App\Enums\Users\ClientCategoryMapTypesEnum;
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
        Schema::create(DatabaseTablesEnum::ClientCategoryMaps->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::RoleId->dbName())->comment('This ID comes from roles table')->index(TableEnum::RoleId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::Roles->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum(TableEnum::MapType->dbName(), ClientCategoryMapTypesEnum::names())->default(ClientCategoryMapTypesEnum::LoyaltyLevel->name);
            $table->string(TableEnum::ItemValue->dbName());
            $table->tinyInteger(TableEnum::Priority->dbName())->default(1);
            $table->boolean(TableEnum::IsActive->dbName())->default(0);
            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();

            $table->unique([TableEnum::RoleId->dbName(), TableEnum::MapType->dbName(), TableEnum::ItemValue->dbName()], 'role_map_type_value_unique');
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_06_24_070507_create_client_category_maps_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ClientCategoryMaps->tableName());
    }
};
