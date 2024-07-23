<?php

namespace Database\Seeders;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\SettingsTableEnum;
use App\Enums\Settings\AppSettingsEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = DatabaseTablesEnum::Settings->tableName();

        DB::table($tableName)->delete();

        foreach (AppSettingsEnum::cases() as $case) {

            DB::table($tableName)->insert([

                SettingsTableEnum::Name->dbName()   => $case->name,
                SettingsTableEnum::Value->dbName()  => $case->defaultValue(false),
                SettingsTableEnum::Cast->dbName()   => $case->castType()->name,

                TimestampsEnum::CreatedAt->dbName()     => \Carbon\Carbon::now(),
                TimestampsEnum::UpdatedAt->dbName()     => \Carbon\Carbon::now(),
            ]);
        }

        // php artisan db:seed --class=SettingSeeder
    }
}
