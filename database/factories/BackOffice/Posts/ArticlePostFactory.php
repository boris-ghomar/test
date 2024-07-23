<?php

namespace Database\Factories\BackOffice\Posts;

use App\Enums\Database\Tables\PostsTableEnum;
use App\HHH_Library\Faker\Persian\classes\PersianFaker;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\Posts\ArticlePost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class ArticlePostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArticlePost::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $maxContentParagraph = 15;

        $persianFaker = new PersianFaker($this->faker);

        $content = "";
        $paragraphCount = rand(2, $maxContentParagraph);

        for ($i = 0; $i < $paragraphCount; $i++) {

            $paragraph = $persianFaker->persianParagraph(rand(3, 20));

            $content = empty($content) ? $paragraph : $content . "\n" . $paragraph;
        }

        return [
            PostsTableEnum::AuthorId->dbName()          => Personnel::all()->random(),
            PostsTableEnum::Title->dbName()             => $persianFaker->persianWords(rand(5, 15), true),
            PostsTableEnum::PostSpaceId->dbName()       => PostSpace::all()->random(),
            PostsTableEnum::Content->dbName()           => $content,
            PostsTableEnum::MetaDescription->dbName()   => $persianFaker->persianWords(rand(10, 15), true),
            PostsTableEnum::IsPublished->dbName()       => Arr::random([0, 1], 1)[0],
            PostsTableEnum::PrivateNote->dbName()       => $persianFaker->persianSentence(rand(5, 15)),
        ];
    }
}
