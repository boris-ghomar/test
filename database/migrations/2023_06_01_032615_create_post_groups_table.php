<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Enums\Posts\TemplatesEnum;
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
        Schema::create(DatabaseTablesEnum::PostGroups->tableName(), function (Blueprint $table) {
            $table->id();
            $table->bigInteger(TableEnum::ParentId->dbName())->default(0)->comment('This ID for groups without a parent is zero.');
            $table->string(TableEnum::Title->dbName());
            $table->text(TableEnum::Description->dbName())->nullable();
            $table->enum(TableEnum::Template->dbName(), TemplatesEnum::names())->nullable();
            $table->string(TableEnum::Photo->dbName())->nullable();
            $table->boolean(TableEnum::IsActive->dbName())->default(1);
            $table->boolean(TableEnum::IsSpace->dbName())->default(0);
            $table->boolean(TableEnum::IsPublicSpace->dbName())->default(0);
            $table->integer(TableEnum::Position->dbName());
            $table->text(TableEnum::PrivateNote->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_06_01_032615_create_post_groups_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::PostGroups->tableName());
    }
};
