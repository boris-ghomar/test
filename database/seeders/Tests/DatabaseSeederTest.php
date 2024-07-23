<?php

namespace Database\Seeders\Tests;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Tests\Bets\BetSeederTest;
use Database\Seeders\Tests\Post\CommentSeederTest;
use Database\Seeders\Tests\Post\LikeSeederTest;
use Database\Seeders\Tests\Post\PostGroupSeederTest;
use Database\Seeders\Tests\Post\PostSeederTest;
use Database\Seeders\Tests\Referral\ReferralSeederTest;
use Database\Seeders\Tests\User\RoleSeederTest;
use Database\Seeders\Tests\User\UserSeederTest;
use Illuminate\Database\Seeder;

class DatabaseSeederTest extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

            DatabaseSeeder::class,

            RoleSeederTest::class,
            // No need to call "PersonnelExtraSeeder::class", it will be call inside of "UserSeeder::class,"
            UserSeederTest::class,

            PostGroupSeederTest::class, // Included: PostCategorySeederTest, PostCategorySeederTest
            PostSeederTest::class, // Included: All Templates: Article, FAQ, VideoGallery, PostGaleery
            CommentSeederTest::class, // Included: All Likable types: Post, Comment
            LikeSeederTest::class, // Included: All Likable types: Post, Comment

            ReferralSeederTest::class,

            BetSeederTest::class, // Included BetSelection Model

        ]);


        // php artisan db:seed --class=Database\Seeders\Tests\DatabaseSeederTest
    }
}
