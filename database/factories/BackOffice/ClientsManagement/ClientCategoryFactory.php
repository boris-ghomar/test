<?php

namespace Database\Factories\BackOffice\ClientsManagement;

use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Users\RoleTypesEnum;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class ClientCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // RolesTableEnum::Name->dbName() => $this->faker->name, // the name will be fill in "RoleSeederTest"
            RolesTableEnum::Type->dbName() => RoleTypesEnum::Site->name,
            RolesTableEnum::IsActive->dbName() => Arr::random([0, 1], 1)[0],
            RolesTableEnum::Descr->dbName() => $this->faker->text(30),
        ];

    }


}
