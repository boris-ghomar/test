<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\JobsTableEnum as TableEnum;
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

        Schema::create(DatabaseTablesEnum::Jobs->tableName(), function (Blueprint $table) {

            $table->bigIncrements(TableEnum::Id->dbName());
            $table->string(TableEnum::Queue->dbName())->index();
            $table->longText(TableEnum::Payload->dbName());
            $table->unsignedTinyInteger(TableEnum::Attempts->dbName());
            $table->unsignedInteger(TableEnum::ReservedAt->dbName())->nullable();
            $table->unsignedInteger(TableEnum::AvailableAt->dbName());
            $table->unsignedInteger(TableEnum::CreatedAt->dbName());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::Jobs->tableName());
    }
};
