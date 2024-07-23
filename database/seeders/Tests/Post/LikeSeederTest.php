<?php

namespace Database\Seeders\Tests\Post;

use Illuminate\Database\Seeder;

class LikeSeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([

            PostLikeSeederTest::class,
            CommentLikeSeederTest::class,

        ]);

        // php artisan db:seed --class=Database\Seeders\Tests\Post\LikeSeederTest
    }
}
