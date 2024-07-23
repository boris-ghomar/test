<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
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
        Schema::table(DatabaseTablesEnum::Comments->tableName(), function (Blueprint $table) {

            $table->foreignId(TableEnum::PostId->dbName())->after(TableEnum::UserId->dbName())->nullable()->constrained(DatabaseTablesEnum::Posts->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This ID comes from posts table');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(DatabaseTablesEnum::Comments->tableName(), function (Blueprint $table) {
            $table->dropForeign(TableEnum::PostId->dbForeignId(DatabaseTablesEnum::Comments));
            $table->dropColumn(TableEnum::PostId->dbName());
        });
    }
};
