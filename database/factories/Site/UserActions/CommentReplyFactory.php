<?php

namespace Database\Factories\Site\UserActions;

use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
use App\Enums\UserActions\CommentableTypesEnum;
use App\HHH_Library\Faker\Persian\classes\PersianFaker;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\Site\UserActions\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class CommentReplyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $persianFaker = new PersianFaker($this->faker);

        return [
            TableEnum::UserId->dbName()             => UserBetconstruct::all()->random(),
            TableEnum::CommentableType->dbName()    => CommentableTypesEnum::Comment->name,
            TableEnum::CommentableId->dbName()      => Comment::all()->unique()->random(),
            TableEnum::Comment->dbName()            => $persianFaker->persianSentence(rand(1, 20)),
            TableEnum::IsApproved->dbName()         => Arr::random([0, 1], 1)[0],
            TableEnum::ApprovedBy->dbName()         => Personnel::all()->random(),

        ];
    }
}
