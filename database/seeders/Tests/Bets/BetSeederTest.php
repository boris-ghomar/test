<?php

namespace Database\Seeders\Tests\Bets;

use App\Models\BackOffice\Bets\Bet;
use Illuminate\Database\Seeder;

class BetSeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $count = 100;

        Bet::factory()
            ->count($count)
            ->create();

        // php artisan db:seed --class=Database\Seeders\Tests\Bets\BetSeederTest
    }
}
