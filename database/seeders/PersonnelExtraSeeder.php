<?php

namespace Database\Seeders;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\PersonnelExtrasTableEnum;
use App\HHH_Library\general\php\Enums\GendersEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonnelExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = DatabaseTablesEnum::PersonnelExtras->tableName();


        DB::table($tableName)->insert([


            PersonnelExtrasTableEnum::UserId->dbName()           => 1,
            PersonnelExtrasTableEnum::FirstName->dbName()        => "Ferhad",
            PersonnelExtrasTableEnum::LastName->dbName()         => "Konar",
            PersonnelExtrasTableEnum::AliasName->dbName()        => "Elliot",
            PersonnelExtrasTableEnum::Gender->dbName()           => GendersEnum::Male->name,

            TimestampsEnum::CreatedAt->dbName()     => \Carbon\Carbon::now(),
            TimestampsEnum::UpdatedAt->dbName()     => \Carbon\Carbon::now(),
        ]);

        // php artisan db:seed --class=PersonnelExtraSeeder
    }
}
