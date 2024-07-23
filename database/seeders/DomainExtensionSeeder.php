<?php

namespace Database\Seeders;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\DomainExtensionsTableEnum as TableEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DomainExtensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = DatabaseTablesEnum::DomainExtensions->tableName();

        $popular = ['com', 'org', 'net', 'click', 'app', 'xyz', 'info', 'dev', 'live', 'pro', 'shop'];
        $radix   = ['online', 'website', 'one', 'host', 'uno', 'site', 'space', 'store', 'tech', 'press'];

        $nameCol = TableEnum::Name->dbName();
        $limitedOrderCol = TableEnum::LimitedOrder->dbName();
        $descrCol = TableEnum::Descr->dbName();
        $createdAtCol = TimestampsEnum::CreatedAt->dbName();
        $updatedAtCol = TimestampsEnum::UpdatedAt->dbName();

        foreach ($popular as $extension) {

            DB::table($tableName)->insert([
                $nameCol            =>  $extension,
                $limitedOrderCol    =>  0,
                $createdAtCol       =>  \Carbon\Carbon::now(),
                $updatedAtCol       => \Carbon\Carbon::now(),
            ]);
        }

        /**
         * Radix domain extensions
         * This provider blocks high number of orders as spam, 300 domains are blocked from us.
         */
        // online,website,one,host,uno, site, space, store, tech, press


        foreach ($radix as $extension) {


            DB::table($tableName)->insert([
                $nameCol            =>  $extension,
                $limitedOrderCol    =>  1,
                $descrCol           =>  'This extension blongs to "Radix" provider. This provider blocks high number of orders as spam, 300 domains are blocked from us.',
                $createdAtCol       =>  \Carbon\Carbon::now(),
                $updatedAtCol       => \Carbon\Carbon::now(),
            ]);
        }

        // php artisan db:seed --class=DomainExtensionSeeder
    }

}
