<?php

namespace Database\Seeders\Tests\Post;

use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Models\BackOffice\PostGrouping\PostCategory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class PostCategorySeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = 30;
        $samplePhotosCount = 15; // sample photos path: public/assets/upload/images/posts

        /**
         * It is placed in the loop so that the next constructor
         * can use the generated ids as parent ids.
         */
        for ($i = 0; $i < $count; $i++) {

            $photoNumber = ($i % $samplePhotosCount) + 1;
            $photoName =  $photoNumber != 4 ? sprintf('small (%s).jpg', $photoNumber) : null; // null assigned for test without photo items

            PostCategory::factory()
                ->count(1)
                ->sequence(fn (Sequence $sequence) => [

                    TableEnum::Photo->dbName() => $photoName,
                    TableEnum::Position->dbName() => $i + 1,

                    /* TableEnum::ParentId->dbName() => 34,
                    TableEnum::IsActive->dbName() => 1,
                    TableEnum::IsSpace->dbName() => 1,
                    TableEnum::IsPublicSpace->dbName() => 1, */
                ])
                ->create();
        }

        // php artisan db:seed --class=Database\Seeders\Tests\Post\PostCategorySeederTest
    }
}
