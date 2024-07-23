<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ReferralRewardItemsTableEnum as TableEnum;
use App\Enums\Referral\ReferralRewardTypeEnum;
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
        Schema::create(DatabaseTablesEnum::ReferralRewardItems->tableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId(TableEnum::PackageId->dbName())->comment('This ID comes from referral_reward_packages table')->constrained(DatabaseTablesEnum::ReferralRewardPackages->tableName())->cascadeOnUpdate()->cascadeOnDelete();
            $table->string(TableEnum::Name->dbName());
            $table->string(TableEnum::DisplayName->dbName());
            $table->enum(TableEnum::Type->dbName(), ReferralRewardTypeEnum::names());
            $table->string(TableEnum::BonusId->dbName(), 30)->nullable()->comment('In case of choose bonus reward type');
            $table->unsignedDecimal(TableEnum::Percentage->dbName(), 5, 2);
            $table->boolean(TableEnum::IsActive->dbName())->default(0);
            $table->tinyInteger(TableEnum::DisplayPriority->dbName())->default(1);
            $table->tinyInteger(TableEnum::PaymentPriority->dbName())->default(1);
            $table->text(TableEnum::PrivateNote->dbName())->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->unique([TableEnum::PackageId->dbName(), TableEnum::Name->dbName()], 'referral_reward_items_unique_row');
        });

        // php artisan migrate:refresh --path=/database/migrations/2024_02_29_044246_create_referral_reward_items_table.php

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::ReferralRewardItems->tableName());
    }
};
