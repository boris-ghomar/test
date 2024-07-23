<?php

namespace Database\Seeders\Tests\Referral;

use App\Enums\Database\Tables\ReferralsTableEnum as TableEnum;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\BackOffice\Referral\Referral;
use Illuminate\Database\Seeder;

class ReferralSeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $clients = UserBetconstruct::all();

        foreach ($clients as $client) {

            $clientId = $client->id;

            if (!Referral::where(TableEnum::UserId->dbName(), $clientId)->exists()) {

                $referralUserIds = Referral::where(TableEnum::UserId->dbName(), '!=', $clientId)
                    ->pluck(TableEnum::UserId->dbName())
                    ->toArray();

                $referredBy = null;
                if (!empty($referralUserIds)) {

                    $referredBy = $referralUserIds[rand(0, count($referralUserIds) - 1)];
                }

                $referral = new Referral();

                $referral->fill([
                    TableEnum::UserId->dbName() => $clientId,
                    TableEnum::ReferredBy->dbName() => $referredBy,
                ]);

                $referral->save();
            }
        }

        // php artisan db:seed --class=Database\Seeders\Tests\Referral\ReferralSeederTest
    }
}
