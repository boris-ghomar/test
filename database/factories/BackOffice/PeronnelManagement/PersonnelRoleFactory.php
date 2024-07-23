<?php

namespace Database\Factories\BackOffice\PeronnelManagement;

use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Users\RoleTypesEnum;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class PersonnelRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PersonnelRole::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // RolesTableEnum::Name->dbName() => $this->faker->name, // the name will be fill in "RoleSeederTest"
            RolesTableEnum::Type->dbName() => RoleTypesEnum::AdminPanel->name,
            RolesTableEnum::IsActive->dbName() => Arr::random([0, 1], 1)[0],
            RolesTableEnum::Descr->dbName() => $this->faker->text(30),
        ];

    }


}
