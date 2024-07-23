<?php

namespace Database\Seeders\Tests\Post;

use Illuminate\Database\Seeder;

class CommentSeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([

            PostCommentSeederTest::class,
            CommentReplySeederTest::class

        ]);

        // php artisan db:seed --class=Database\Seeders\Tests\Post\CommentSeederTest
    }
}
