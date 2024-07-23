<?php

namespace Database\Seeders\Tests\User;

use App\Models\BackOffice\PeronnelManagement\Personnel;
use Illuminate\Database\Seeder;

class UserSeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = 20;

        Personnel::factory()->count($count)->create();

        // php artisan db:seed --class=Database\Seeders\Tests\User\UserSeederTest
    }
}
