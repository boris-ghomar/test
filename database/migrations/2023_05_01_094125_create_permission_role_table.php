<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PermissionRoleTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::PermissionRole->tableName(), function (Blueprint $table) {

            $table->foreignId(TableEnum::PermissionId->dbName())->index(TableEnum::PermissionId->dbName().'_foreign')->constrained(DatabaseTablesEnum::Permissions->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::RoleId->dbName())->index(TableEnum::RoleId->dbName().'_foreign')->constrained(DatabaseTablesEnum::Roles->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean(TableEnum::IsActive->dbName())->default(0);
            $table->string(TableEnum::Descr->dbName())->nullable();
            $table->unique([TableEnum::PermissionId->dbName(), TableEnum::RoleId->dbName()], 'unique_row');
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_05_01_094125_create_permission_role_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::PermissionRole->tableName());
    }
};
