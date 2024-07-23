<?php

namespace Database\Factories\BackOffice\PostGrouping;

use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\HHH_Library\Faker\Persian\classes\PersianFaker;
use App\Models\BackOffice\PostGrouping\PostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class PostCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PostCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $persianFaker = new PersianFaker($this->faker);

        return [
            PostGroupsTableEnum::ParentId->dbName() => $this->getParentId(),
            PostGroupsTableEnum::Title->dbName() => $persianFaker->persianWords(rand(2, 6), true),
            PostGroupsTableEnum::Description->dbName() => $persianFaker->persianSentence(rand(10, 20)),
            PostGroupsTableEnum::IsSpace->dbName() => 0,
            PostGroupsTableEnum::IsPublicSpace->dbName() => 0,
            PostGroupsTableEnum::IsActive->dbName() => Arr::random([0, 1], 1)[0],
            PostGroupsTableEnum::PrivateNote->dbName() => $persianFaker->persianSentence(rand(5, 15)),
        ];
    }

    /**
     * get random parent id
     *
     * @return int
     */
    private function getParentId(): int
    {
        $primaryKey = PostGroupsTableEnum::Id->dbName();
        $parentIds = PostCategory::all([$primaryKey])->pluck($primaryKey)->toArray();

        array_push($parentIds, 0);

        return Arr::random($parentIds, 1)[0];
    }
}
