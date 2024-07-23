<?php

namespace Database\Seeders\Tests\User;

use App\Enums\Database\Tables\RolesTableEnum;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class RoleSeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Admin Roles
        $roles = ['Admin', 'CEO', 'CFO', 'Support', 'Developer'];
        $count = count($roles);

        PersonnelRole::factory()
            ->count($count)
            ->sequence(fn (Sequence $sequence) => [RolesTableEnum::Name->dbName() => $roles[$sequence->index]])
            ->create();

        // Site roles
        $categories = ['VIP', 'Gold', 'Silver', 'BlackList'];
        $count = count($categories);

        ClientCategory::factory()
            ->count($count)
            ->sequence(fn (Sequence $sequence) => [RolesTableEnum::Name->dbName() => $categories[$sequence->index]])
            ->create();

        // php artisan db:seed --class=Database\Seeders\Tests\User\RoleSeederTest
    }
}
