<?php

namespace Database\Seeders\Tests\Post;

use Illuminate\Database\Seeder;

class PostGroupSeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([

            PostCategorySeederTest::class,
            PostSpaceSeederTest::class,

        ]);

        // php artisan db:seed --class=Database\Seeders\Tests\Post\PostGroupSeederTest
    }
}
