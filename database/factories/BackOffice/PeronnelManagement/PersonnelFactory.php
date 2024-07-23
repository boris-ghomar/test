<?php

namespace Database\Factories\BackOffice\PeronnelManagement;

use App\Enums\Database\Defaults\RememberTokenEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Enums\Users\UsersTypesEnum;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

class PersonnelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Personnel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            UsersTableEnum::Username->dbName() => $this->faker->userName(),
            UsersTableEnum::Email->dbName() => $this->faker->unique()->safeEmail(),
            UsersTableEnum::EmailVerifiedAt->dbName()  => now(),
            UsersTableEnum::Password->dbName()  => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            RememberTokenEnum::TwoFactorSecret->dbName()  => null,
            RememberTokenEnum::TwoFactorRecoveryCodes->dbName()  => null,
            RememberTokenEnum::RememberToken->dbName()  => Str::random(10),
            UsersTableEnum::ProfilePhotoName->dbName() => null,
            UsersTableEnum::CurrentTeamId->dbName() => null,
            UsersTableEnum::Type->dbName() => UsersTypesEnum::Personnel->name,
            UsersTableEnum::RoleId->dbName() => PersonnelRole::all()->random(),
            UsersTableEnum::Status->dbName() => Arr::random(UsersStatusEnum::names(), 1)[0],
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                UsersTableEnum::EmailVerifiedAt->dbName()  => null,
            ];
        });
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(callable $callback = null): static
    {
        if (!Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name . '\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }
}
