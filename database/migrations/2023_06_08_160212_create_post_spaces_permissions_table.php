<?php

use App\Enums\AccessControl\PostActionsEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum as TableEnum;
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
        Schema::create(DatabaseTablesEnum::PostSpacesPermissions->tableName(), function (Blueprint $table) {
            $table->uuid(TableEnum::Id->dbName());
            $table->foreignId(TableEnum::PostSpaceId->dbName())->index(TableEnum::PostSpaceId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::PostGroups->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(TableEnum::ClientCategoryId->dbName())->index(TableEnum::ClientCategoryId->dbName() . '_foreign')->constrained(DatabaseTablesEnum::Roles->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum(TableEnum::PostAction->dbName(), PostActionsEnum::names());
            $table->boolean(TableEnum::IsActive->dbName())->default(0);
            $table->string(TableEnum::Descr->dbName())->nullable();

            $table->unique([TableEnum::PostSpaceId->dbName(), TableEnum::ClientCategoryId->dbName(), TableEnum::PostAction->dbName()], 'unique_row');
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_06_08_160212_create_post_spaces_permissions_table.php

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::PostSpacesPermissions->tableName());
    }
};
