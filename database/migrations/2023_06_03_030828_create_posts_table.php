<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::Posts->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::PostSpaceId->dbName())->comment('This id comes from post_groups table')->constrained(DatabaseTablesEnum::PostGroups->tableName())->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId(TableEnum::AuthorId->dbName())->comment('This id comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId(TableEnum::EditorId->dbName())->nullable()->comment('This id comes from users table')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->restrictOnDelete();
            $table->string(TableEnum::Title->dbName());
            $table->longText(TableEnum::Content->dbName())->nullable();
            $table->string(TableEnum::MainPhoto->dbName())->nullable();
            $table->string(TableEnum::MetaDescription->dbName())->nullable();
            $table->boolean(TableEnum::IsPublished->dbName())->default(0);
            $table->boolean(TableEnum::IsPinned->dbName())->default(0);
            $table->tinyInteger(TableEnum::PinNumber->dbName())->default(0);
            $table->text(TableEnum::PrivateNote->dbName())->nullable();
            $table->unsignedBigInteger(TableEnum::Views->dbName())->default(0);
            $table->timestamp(TableEnum::ContentUpdatedAt->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_06_03_030828_create_posts_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Posts->tableName());
    }
};
