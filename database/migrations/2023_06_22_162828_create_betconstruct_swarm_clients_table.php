<?php

use App\Enums\Database\DatabaseTablesEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ClientSwarmModelEnum as TableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\GendersEnum;
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
        Schema::create(DatabaseTablesEnum::BetconstructSwarmClients->tableName(), function (Blueprint $table) {
            $table->unsignedBigInteger(TableEnum::Id->dbName())->primary()->comment('User ID in betconstruct database.');
            $table->foreignId(TableEnum::UserId->dbName())->unique('unique_client_user')->constrained(DatabaseTablesEnum::Users->tableName())->cascadeOnUpdate()->cascadeOnDelete()->comment('This id comes from users table');
            $table->string(TableEnum::Username->dbName())->comment('username of client in betconstruct');
            $table->string(TableEnum::Email->dbName())->nullable();
            $table->string(TableEnum::FirstName->dbName(), 100)->nullable();
            $table->string(TableEnum::LastName->dbName(), 100)->nullable();
            $table->string(TableEnum::MiddleName->dbName(), 100)->nullable();
            $table->string(TableEnum::Name->dbName())->comment('LastName + FirstName + MiddleName')->nullable();
            $table->string(TableEnum::Phone->dbName(), 50)->nullable();
            $table->string(TableEnum::MobilePhone->dbName(), 50)->nullable();
            $table->dateTime(TableEnum::BirthDate->dbName())->nullable();
            $table->string(TableEnum::Gender->dbName(), 7)->nullable()->comment(GendersEnum::json());
            $table->string(TableEnum::Language->dbName(), 3)->nullable()->comment('prefered language of client, ISO 639-1 codes: fa, en,...');
            $table->string(TableEnum::PersonalId->dbName(), 25)->nullable();
            $table->tinyInteger(TableEnum::Status->dbName())->default(1);
            $table->integer(TableEnum::DepositCount->dbName())->nullable();

            $table->integer(TableEnum::ActiveTimeInCasino->dbName())->nullable();

            $table->string(TableEnum::BirthRegion->dbName(), 3)->nullable()->comment('ISO ALPHA-2 Code of country (FR, GB,RU)');
            $table->string(TableEnum::CountryCode->dbName(), 3)->nullable()->comment('ISO ALPHA-2 Code of country (FR, GB,RU)');
            $table->string(TableEnum::Province->dbName(), 40)->nullable();
            $table->string(TableEnum::City->dbName(), 40)->nullable();
            $table->string(TableEnum::Address->dbName())->nullable();
            $table->string(TableEnum::AdditionalAddress->dbName())->nullable();
            $table->string(TableEnum::ZipCode->dbName(), 15)->nullable();

            $table->unsignedBigInteger(TableEnum::AffiliateId->dbName())->nullable();
            $table->string(TableEnum::Btag->dbName())->nullable();

            $table->string(TableEnum::Currency->dbName(), 3)->nullable()->comment('ISO 4217 code of currency (USD, EUR,RUB, ..)');
            $table->float(TableEnum::Balance->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::UnplayedBalance->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::BonusBalance->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::FrozenBalance->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::BonusMoney->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::BonusWinBalance->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::SportBonus->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::CasinoBalance->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::CasinoUnplayedBalance->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::CasinoBonus->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::CasinoBonusWin->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::CasinoMaximalDailyBet->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::CasinoMaximalSingleBet->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::CounterOfferMinAmount->dbName(), 22, 2)->nullable();

            $table->boolean(TableEnum::HasFreeBets->dbName())->default(0);

            $table->string(TableEnum::Iban->dbName(), 30)->nullable()->comment('international bank account number of client');
            $table->string(TableEnum::SwiftCode->dbName(), 40)->nullable();
            $table->boolean(TableEnum::IsTaxApplicable->dbName())->default(0);

            $table->boolean(TableEnum::IsVerified->dbName())->default(0);
            $table->boolean(TableEnum::IsAgent->dbName())->default(0);

            $table->timestamp(TableEnum::LastLoginDate->dbName())->nullable();

            $table->integer(TableEnum::LoyaltyLevelId->dbName())->nullable();
            $table->float(TableEnum::LoyaltyPoint->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::LoyaltyEarnedPoints->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::LoyaltyExchangedPoints->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::LoyaltyLastEarnedPoints->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::LoyaltyMaxExchangePoint->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::LoyaltyMinExchangePoint->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::LoyaltyPointUsagePeriod->dbName(), 22, 2)->nullable();

            $table->float(TableEnum::MaximalDailyBet->dbName(), 22, 2)->nullable();
            $table->float(TableEnum::MaximalSingleBet->dbName(), 22, 2)->nullable();

            $table->dateTime(TableEnum::RegDate->dbName())->nullable();
            $table->unsignedBigInteger(TableEnum::SportsbookProfileId->dbName())->nullable();

            $table->tinyInteger(TableEnum::AuthenticationStatus->dbName())->nullable();
            $table->boolean(TableEnum::IsTwoFactorAuthenticationEnabled->dbName())->default(0);

            $table->boolean(TableEnum::SubscribeToBonus->dbName())->default(0);
            $table->boolean(TableEnum::SubscribeToEmail->dbName())->default(0);
            $table->boolean(TableEnum::SubscribeToSms->dbName())->default(0);
            $table->boolean(TableEnum::SubscribedToNews->dbName())->default(0);
            $table->boolean(TableEnum::SubscribeToInternalMessage->dbName())->default(0);
            $table->boolean(TableEnum::SubscribeToPhoneCall->dbName())->default(0);
            $table->boolean(TableEnum::SubscribeToPushNotification->dbName())->default(0);

            $table->boolean(TableEnum::IsBonusAllowed->dbName())->default(0);
            $table->boolean(TableEnum::IsCashOutAvailable->dbName())->default(0);
            $table->boolean(TableEnum::IsGdprPassed->dbName())->default(0);
            $table->boolean(TableEnum::IsPhoneVerified->dbName())->default(0);
            $table->boolean(TableEnum::IsMobilePhoneVerified->dbName())->default(0);
            $table->boolean(TableEnum::IsSuperBetAvailable->dbName())->default(0);

            $table->json(TableEnum::Wallets->dbName())->nullable();
            $table->json(TableEnum::SupportedCurrencies->dbName())->nullable();

            $table->text(TableEnum::Descr->dbName())->nullable();
            $table->timestamps();
        });

        // php artisan migrate:refresh --path=/database/migrations/2023_06_22_162828_create_betconstruct_swarm_clients_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DatabaseTablesEnum::BetconstructSwarmClients->tableName());
    }
};

// int|None Exmp:
// string|None Exmp:
// float Exmp:
