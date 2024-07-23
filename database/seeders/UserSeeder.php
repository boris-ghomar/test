<?php

namespace Database\Seeders;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\SystemReserved\AdminRoleReservedEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Enums\Users\UsersTypesEnum;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = DatabaseTablesEnum::Users->tableName();

        DB::table($tableName)->insert([

            UsersTableEnum::Id->dbName()            => 1,
            UsersTableEnum::Username->dbName()      => "FerhadKonar",
            UsersTableEnum::Email->dbName()         => "ferhadkonar@gmail.com",
            UsersTableEnum::Password->dbName()      => Hash::make('Parsa5180#'),
            UsersTableEnum::Type->dbName()          => UsersTypesEnum::Personnel->name,
            UsersTableEnum::RoleId->dbName()        => AdminRoleReservedEnum::SuperAdmin->model()->id,
            UsersTableEnum::Status->dbName()        => UsersStatusEnum::Active->name,

            TimestampsEnum::CreatedAt->dbName()     => \Carbon\Carbon::now(),
            TimestampsEnum::UpdatedAt->dbName()     => \Carbon\Carbon::now(),
        ]);

        // php artisan db:seed --class=UserSeeder
    }
}
