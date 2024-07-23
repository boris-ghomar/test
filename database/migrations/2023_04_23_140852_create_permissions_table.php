<?php

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\AccessControl\PermissionTypeEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PermissionsTableEnum;
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
        Schema::create(DatabaseTablesEnum::Permissions->tableName(), function (Blueprint $table) {
            $table->id();
            $table->string(PermissionsTableEnum::Route->dbName());
            $table->enum(PermissionsTableEnum::Ability->dbName(), PermissionAbilityEnum::names());
            $table->enum(PermissionsTableEnum::Type->dbName(), PermissionTypeEnum::names())->default(PermissionTypeEnum::Site->name);
            $table->boolean(PermissionsTableEnum::IsActive->dbName())->default(0);
            $table->string(PermissionsTableEnum::Descr->dbName())->nullable();

            $table->timestamps();
            $table->unique([PermissionsTableEnum::Route->dbName(), PermissionsTableEnum::Ability->dbName()], 'unique_row');
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_04_23_140852_create_permissions_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Permissions->tableName());
    }
};
