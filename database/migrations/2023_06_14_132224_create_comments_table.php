<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
use App\Enums\UserActions\CommentableTypesEnum;
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
        Schema::create(DatabaseTablesEnum::Comments->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::UserId->dbName())->index(TableEnum::UserId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This ID comes from users table');
            $table->enum(TableEnum::CommentableType->dbName(), CommentableTypesEnum::names());
            $table->foreignId(TableEnum::CommentableId->dbName());
            $table->text(TableEnum::Comment->dbName());
            $table->boolean(TableEnum::IsApproved->dbName())->default(0);
            $table->foreignId(TableEnum::ApprovedBy->dbName())->index(TableEnum::ApprovedBy->dbName() . '_foreign')->nullable()->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->nullOnDelete()->comment('This ID comes from users table and blong to personnel.');
            $table->boolean(TableEnum::IsAdminAnswer->dbName())->default(0);
            $table->boolean(TableEnum::IsNotifiedPublished->dbName())->default(0);
            $table->boolean(TableEnum::IsNotifiedCommentableOwner->dbName())->default(0);
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_06_14_132224_create_comments_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Comments->tableName());
    }
};
