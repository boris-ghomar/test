<?php

namespace Database\Seeders\Tests\Post;

use App\Enums\Database\Tables\LikesTableEnum  as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\BackOffice\Posts\Post;
use Database\Factories\Site\UserActions\PostLikeFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class PostLikeSeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $count = Post::count() / 2;

        foreach (UserBetconstruct::all() as $client) {
            (new PostLikeFactory)
                ->count($count)
                ->sequence(fn (Sequence $sequence) => [

                    TableEnum::UserId->dbName() => $client[UsersTableEnum::Id->dbName()],
                ])
                ->create();
        }

        // php artisan db:seed --class=Database\Seeders\Tests\Post\PostLikeSeederTest
    }
}
