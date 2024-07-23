<?php

namespace Database\Seeders;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\SystemReserved\AdminRoleReservedEnum;
use App\Enums\SystemReserved\ClientCategoryReservedEnum;
use App\Enums\Users\RoleTypesEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = DatabaseTablesEnum::Roles->tableName();

        foreach (AdminRoleReservedEnum::cases() as $case) {

            $this->insertRole($tableName, $case);
        }

        foreach (ClientCategoryReservedEnum::cases() as $case) {

            $this->insertRole($tableName, $case);
        }

        // php artisan db:seed --class=RoleSeeder
    }

    /**
     * Insert role to database
     *
     * @param  string $tableName
     * @param  mixed $case
     * @return void
     */
    private function insertRole(string $tableName, mixed $case): void
    {

        DB::table($tableName)->insert([

            RolesTableEnum::Name->dbName()         => $case->value,
            RolesTableEnum::Type->dbName()         => $case->type(false),
            RolesTableEnum::IsActive->dbName()     => true,
            RolesTableEnum::Descr->dbName()        => $case->description(),


            TimestampsEnum::CreatedAt->dbName()     => \Carbon\Carbon::now(),
            TimestampsEnum::UpdatedAt->dbName()     => \Carbon\Carbon::now(),
        ]);
    }
}
