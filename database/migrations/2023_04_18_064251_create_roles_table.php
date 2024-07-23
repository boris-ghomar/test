<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Users\RoleTypesEnum;
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
        Schema::create(DatabaseTablesEnum::Roles->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(RolesTableEnum::Name->dbName());
            $table->string(RolesTableEnum::DisplayName->dbName())->nullable()->comment('If the value of this name is not empty, the system will display this name to users instead of the original name.');
            $table->enum(RolesTableEnum::Type->dbName(), RoleTypesEnum::names());
            $table->boolean(RolesTableEnum::IsActive->dbName())->default(1);
            $table->string(RolesTableEnum::Descr->dbName())->nullable();
            $table->timestamps();

            $table->unique([RolesTableEnum::Name->dbName(), RolesTableEnum::Type->dbName()], 'roles_name_type_unique');
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_04_18_064251_create_roles_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Roles->tableName());
    }
};
