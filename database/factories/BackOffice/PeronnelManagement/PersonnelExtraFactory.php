<?php

namespace Database\Factories\BackOffice\PeronnelManagement;


use App\Enums\Database\Tables\PersonnelExtrasTableEnum;
use App\HHH_Library\general\php\Enums\GendersEnum;
use App\Models\BackOffice\PeronnelManagement\PersonnelExtra;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class PersonnelExtraFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PersonnelExtra::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $gender = Arr::random(GendersEnum::names(), 1)[0];

        return [

            PersonnelExtrasTableEnum::FirstName->dbName() => $this->faker->firstName($gender),
            PersonnelExtrasTableEnum::LastName->dbName() => $this->faker->lastName($gender),
            PersonnelExtrasTableEnum::AliasName->dbName() => $this->faker->unique()->firstName($gender),
            PersonnelExtrasTableEnum::Gender->dbName() => $gender,
            PersonnelExtrasTableEnum::Descr->dbName() => $this->faker->text(50),

        ];
    }
}
