<?php

namespace Database\Factories\BackOffice\Bets;

use App\Enums\Bets\BetSelectionStatusEnum;
use App\Enums\Database\Tables\BetSelectionsTableEnum as TableEnum;
use App\Models\BackOffice\Bets\BetSelection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class BetSelectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BetSelection::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marketNames = ['Match Result', 'Double Chance', 'Total Goals'];
        $selectionNames = [
            'Match Result'  => ['W1', 'W2'],
            'Double Chance' => ['1X', '2X'],
            'Total Goals'   => ['Over (0.5)', 'Over (1.5)', 'Under (0.5)', 'Under (1.5)'],
        ];

        $sportNames = ['Football', 'Basketball', 'Volley', 'Tennis'];
        $sportName = Arr::random($sportNames, 1)[0];

        $marketName = Arr::random($marketNames, 1)[0];
        $selectionName = Arr::random($selectionNames[$marketName], 1)[0];


        return [

            TableEnum::SelectionId->dbName() => $this->faker->numberBetween(1000000),
            TableEnum::SelectionName->dbName() => $selectionName,
            TableEnum::MarketId->dbName() => $this->faker->numberBetween(1000000),
            TableEnum::MarketName->dbName() => $marketName,
            TableEnum::MatchId->dbName() => $this->faker->numberBetween(1000000),
            TableEnum::MatchShortId->dbName() => $this->faker->numberBetween(10000),
            TableEnum::MatchName->dbName() => sprintf('%s -%s', $this->faker->firstName(), $this->faker->firstName()),
            TableEnum::RegionId->dbName() => $this->faker->numberBetween(1, 300),
            TableEnum::RegionName->dbName() => $this->faker->country(),
            TableEnum::CompetitionId->dbName() => $this->faker->numberBetween(1, 9999),
            TableEnum::CompetitionName->dbName() => $this->faker->firstName() . " cup",
            TableEnum::SportId->dbName() => $this->faker->numberBetween(1, 30),
            TableEnum::SportName->dbName() => $sportName,
            TableEnum::SportAlias->dbName() => $sportName,
            TableEnum::Odds->dbName() => $this->faker->randomFloat(2, 1.01, 3),
            TableEnum::Basis->dbName() => 0,
            TableEnum::IsLive->dbName() => Arr::random([0, 1], 1)[0],
            TableEnum::Status->dbName() => Arr::random(BetSelectionStatusEnum::names(), 1)[0],
            TableEnum::MatchStartDate->dbName() => now()->addMinutes(rand(1, 1440))->toDateTimeString(),
        ];
    }
}
