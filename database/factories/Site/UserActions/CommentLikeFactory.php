<?php

namespace Database\Factories\Site\UserActions;

use App\Enums\Database\Tables\LikesTableEnum as TableEnum;
use App\Enums\UserActions\LikableTypesEnum;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\Site\UserActions\Comment;
use App\Models\Site\UserActions\Like;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentLikeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Like::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            TableEnum::UserId->dbName()         => UserBetconstruct::all()->random(),
            TableEnum::LikableType->dbName()    => LikableTypesEnum::Comment->name,
            TableEnum::LikableId->dbName()      => Comment::all()->unique()->random(),
        ];
    }
}
