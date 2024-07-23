<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\GendersEnum;
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
        Schema::create(DatabaseTablesEnum::BetconstructClients->tableName(), function (Blueprint $table) {

            $table->unsignedBigInteger(ClientModelEnum::Id->dbName())->primary()->comment('User ID in betconstruct database.');
            $table->foreignId(ClientModelEnum::UserId->dbName())->unique('unique_client_user')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This id comes from users table');
            $table->string(ClientModelEnum::Login->dbName())->comment('username of client in website');
            $table->text(ClientModelEnum::Password->dbName())->nullable();
            $table->string(ClientModelEnum::Email->dbName())->nullable();
            $table->string(ClientModelEnum::FirstName->dbName(), 100)->nullable();
            $table->string(ClientModelEnum::LastName->dbName(), 100)->nullable();
            $table->string(ClientModelEnum::MiddleName->dbName(), 100)->nullable();
            $table->string(ClientModelEnum::Name->dbName())->comment('LastName + FirstName + MiddleName')->nullable();
            $table->string(ClientModelEnum::NickName->dbName(), 100)->nullable();
            $table->string(ClientModelEnum::Phone->dbName(), 50)->nullable();
            $table->string(ClientModelEnum::MobilePhone->dbName(), 50)->nullable();
            $table->dateTime(ClientModelEnum::BirthDateStamp->dbName())->nullable();
            $table->unsignedTinyInteger(ClientModelEnum::Gender->dbName())->default(GendersEnum::Unknown->value)->comment(GendersEnum::json());
            $table->string(ClientModelEnum::Language->dbName(), 3)->nullable()->comment('prefered language of client, ISO 639-1 codes: fa, en,...');
            $table->string(ClientModelEnum::RegionCode->dbName(), 3)->nullable()->comment('ISO ALPHA-2 Code of country (FR, GB,RU)');
            $table->float(ClientModelEnum::TimeZone->dbName(), 4, 2)->nullable();
            $table->unsignedBigInteger(ClientModelEnum::ProfileId->dbName())->nullable();
            $table->string(ClientModelEnum::DocNumber->dbName(), 40)->nullable()->comment('Passport Number of client');
            $table->string(ClientModelEnum::PersonalId->dbName(), 40)->nullable()->comment('Unique identity number of client');
            $table->string(ClientModelEnum::BTag->dbName())->nullable();
            $table->boolean(ClientModelEnum::IsTest->dbName())->default(0);
            $table->boolean(ClientModelEnum::IsLocked->dbName())->default(0);
            $table->boolean(ClientModelEnum::IsSubscribedToNewsletter->dbName())->default(0);
            $table->boolean(ClientModelEnum::IsVerified->dbName())->default(0);

            $table->string(ClientModelEnum::CurrencyId->dbName(), 3)->nullable()->comment('ISO 4217 code of currency (USD, EUR,RUB, ..)');
            $table->float(ClientModelEnum::Balance->dbName(), 22, 2)->nullable();
            $table->float(ClientModelEnum::UnplayedBalance->dbName(), 22, 2)->nullable();
            $table->string(ClientModelEnum::IBAN->dbName(), 30)->nullable()->comment('international bank account number of client');
            $table->string(ClientModelEnum::LastLoginIp->dbName(), 50)->nullable();
            $table->timestamp(ClientModelEnum::LastLoginTimeStamp->dbName())->nullable();
            $table->string(ClientModelEnum::City->dbName(), 50)->nullable();
            $table->text(ClientModelEnum::Address->dbName())->nullable();
            $table->string(ClientModelEnum::PromoCode->dbName())->nullable();
            $table->string(ClientModelEnum::ExtAgentId->dbName())->nullable();
            $table->dateTime(ClientModelEnum::CreatedStamp->dbName())->nullable();
            $table->dateTime(ClientModelEnum::ModifiedStamp->dbName())->nullable();
            $table->dateTime(ClientModelEnum::ExcludedStamp->dbName())->nullable();
            $table->string(ClientModelEnum::RFId->dbName())->nullable();
            $table->string(ClientModelEnum::ResetCode->dbName())->nullable();
            $table->timestamp(ClientModelEnum::ResetExpireDateStamp->dbName())->nullable();
            $table->string(ClientModelEnum::DocIssuedBy->dbName())->nullable();
            $table->float(ClientModelEnum::PreMatchSelectionLimit->dbName(), 13, 2)->nullable();
            $table->float(ClientModelEnum::LiveSelectionLimit->dbName(), 13, 2)->nullable();
            $table->unsignedBigInteger(ClientModelEnum::SportsbookProfileId->dbName())->nullable();
            $table->integer(ClientModelEnum::GlobalLiveDelay->dbName())->nullable();
            $table->timestamp(ClientModelEnum::ExcludedLastStamp->dbName())->nullable();
            $table->string(ClientModelEnum::ExternalId->dbName())->nullable();
            $table->string(ClientModelEnum::ZipCode->dbName())->nullable();
            $table->string(ClientModelEnum::TermsAndConditionsVersion->dbName())->nullable();
            $table->boolean(ClientModelEnum::IsExcludedFromBonuses->dbName())->default(0);
            $table->string(ClientModelEnum::CustomPlayerCategory->dbName())->nullable();

            $table->boolean(ClientModelEnum::CanLogin->dbName())->nullable();
            $table->boolean(ClientModelEnum::CanDeposit->dbName())->nullable();
            $table->boolean(ClientModelEnum::CanWithdraw->dbName())->nullable();
            $table->boolean(ClientModelEnum::CanBet->dbName())->nullable();

            $table->integer(ClientModelEnum::DepositCount->dbName())->nullable();
            $table->text(ClientModelEnum::Descr->dbName())->nullable();
            $table->timestamp(ClientModelEnum::MobileVerifiedAtInternal->dbName())->nullable();
            $table->string(ClientModelEnum::ProvinceInternal->dbName())->nullable();
            $table->string(ClientModelEnum::CityInternal->dbName())->nullable();
            $table->string(ClientModelEnum::JobFieldInternal->dbName())->nullable();
            $table->json(ClientModelEnum::ContactNumbersInternal->dbName())->nullable();
            $table->json(ClientModelEnum::ContactMethodsInternal->dbName())->nullable();
            $table->json(ClientModelEnum::CallerGenderInternal->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_04_22_100604_create_betconstruct_clients_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::BetconstructClients->tableName());
    }
};
