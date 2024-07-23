<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\UsersTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Enums\Users\UsersTypesEnum;
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

        Schema::table(DatabaseTablesEnum::Users->tableName(), function (Blueprint $table) {

            $table->renameColumn('name', TableEnum::Username->dbName());
            $table->renameColumn('profile_photo_path', TableEnum::ProfilePhotoName->dbName());
            $table->softDeletes();
            $table->enum(TableEnum::Type->dbName(), UsersTypesEnum::names())->default(UsersTypesEnum::BetconstructClient->name);
            $table->foreignId(TableEnum::RoleId->dbName())->constrained(DatabaseTablesEnum::Roles->tableName())->cascadeOnUpdate()->restrictOnDelete()->comment('This id comes from roles table');
            $table->enum(UsersTableEnum::Status->dbName(), UsersStatusEnum::names());

            $table->dropUnique('users_email_unique');
            $table->unique([TableEnum::Username->dbName(), TableEnum::Type->dbName()], 'users_username_type_unique');
            $table->unique([TableEnum::Email->dbName(), TableEnum::Type->dbName()], 'users_email_type_unique');
        });


        // php artisan migrate:refresh --path=/database/migrations/2023_04_19_053824_modify_users_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(DatabaseTablesEnum::Users->tableName(), function (Blueprint $table) {

            $table->dropUnique('users_username_type_unique');
            $table->dropUnique('users_email_type_unique');

            $table->renameColumn(TableEnum::Username->dbName(), 'name');
            $table->renameColumn(TableEnum::ProfilePhotoName->dbName(), 'profile_photo_path');
            $table->dropSoftDeletes();
            $table->dropColumn(TableEnum::Type->dbName());
            $table->dropForeign(TableEnum::RoleId->dbForeignId(DatabaseTablesEnum::Users));
            $table->dropColumn(TableEnum::RoleId->dbName());
            $table->dropColumn(TableEnum::Status->dbName());

            $table->unique(TableEnum::Email->dbName(), 'users_email_unique');
        });
    }
};
