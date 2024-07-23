<?php

namespace Database\Seeders\Tests\Post;

use Illuminate\Database\Seeder;

class PostSeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([

            ArticlePostSeederTest::class,
            FaqPostSeederTest::class,

        ]);

        // php artisan db:seed --class=Database\Seeders\Tests\Post\PostSeederTest
    }
}
