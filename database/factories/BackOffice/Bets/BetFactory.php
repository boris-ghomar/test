<?php

namespace Database\Factories\BackOffice\Bets;

use App\Enums\Bets\BetContextEnum;
use App\Enums\Bets\BetStatusEnum;
use App\Enums\Bets\BetTypeEnum;
use App\Enums\Database\Tables\BetSelectionsTableEnum;
use App\Enums\Database\Tables\BetsTableEnum as TableEnum;
use App\Enums\General\CurrencyEnum;
use App\Enums\General\PartnerEnum;
use App\Models\BackOffice\Bets\Bet;
use App\Models\BackOffice\Bets\BetSelection;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class BetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $betDetails = $this->makeBetDetails();

        return [
            TableEnum::UserId->dbName() => UserBetconstruct::all()->random()->id,
            TableEnum::Partner->dbName() => PartnerEnum::Betconstruct->name,
            TableEnum::Context->dbName() => Arr::random(BetContextEnum::names(), 1)[0],
            TableEnum::PartnerBetId->dbName() => $this->faker->numberBetween(1000000, 2147483647),
            TableEnum::BetType->dbName() => Arr::random(BetTypeEnum::names(), 1)[0],
            TableEnum::TransactionId->dbName() => $this->faker->numberBetween(1000000, 2147483647),
            TableEnum::Amount->dbName() => $betDetails[TableEnum::Amount->dbName()],
            TableEnum::WinAmount->dbName() => $betDetails[TableEnum::WinAmount->dbName()],
            TableEnum::Odds->dbName() => $betDetails[TableEnum::Odds->dbName()],
            TableEnum::Status->dbName() => $betDetails[TableEnum::Status->dbName()],
            TableEnum::BonusId->dbName() => null,
            TableEnum::BonusBetAmount->dbName() => null,
            TableEnum::CashoutAmount->dbName() => null,
            TableEnum::IsLive->dbName() => Arr::random([0, 1], 1)[0],
            TableEnum::Currency->dbName() => $betDetails[TableEnum::Currency->dbName()],
            TableEnum::PlacedAt->dbName() => $betDetails[TableEnum::PlacedAt->dbName()],
            TableEnum::CalculatedAt->dbName() => $betDetails[TableEnum::CalculatedAt->dbName()],
            TableEnum::PaidAt->dbName() => $betDetails[TableEnum::PaidAt->dbName()],
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Bet $bet) {
            // ...
        })->afterCreating(function (Bet $bet) {
            $this->createBetSelections($bet);
        });
    }


    /**
     * Make bet details
     *
     * @return array
     */
    private function makeBetDetails(): array
    {
        $betDetails = [];

        $currencyCol = TableEnum::Currency->dbName();
        $statusCol = TableEnum::Status->dbName();
        $oddsCol = TableEnum::Odds->dbName();
        $amountCol = TableEnum::Amount->dbName();
        $winAmountCol = TableEnum::WinAmount->dbName();
        $placedAtCol = TableEnum::PlacedAt->dbName();
        $calculatedAtCol = TableEnum::CalculatedAt->dbName();
        $paidAtCol = TableEnum::PaidAt->dbName();

        $status = Arr::random(BetStatusEnum::names(), 1)[0];
        $betDetails[$currencyCol] = Arr::random(CurrencyEnum::names(), 1)[0];
        $betDetails[$statusCol] = $status;
        $betDetails[$oddsCol] = $this->faker->randomFloat(2, 1.01, 3);
        $betDetails[$placedAtCol] = now()->subDays(rand(1, 30))->toDateTimeString();
        $betDetails[$calculatedAtCol] = Carbon::parse($betDetails[$placedAtCol])->addHours(rand(1, 24))->toDateTimeString();

        $betDetails[$amountCol] = match ($betDetails[$currencyCol]) {
            CurrencyEnum::USD->name => $this->faker->randomFloat(2, 1, 1000),
            CurrencyEnum::IRT->name => $this->faker->numberBetween(5, 10000),
            CurrencyEnum::TOM->name => $this->faker->numberBetween(50000, 10000000),
            CurrencyEnum::IRR->name => $this->faker->numberBetween(500000, 100000000),

            default => $this->faker->numberBetween(100, 200)
        };

        switch ($status) {
            case BetStatusEnum::Accepted->name:
                $betDetails[$winAmountCol] = $betDetails[$amountCol] * $betDetails[$oddsCol];
                $betDetails[$paidAtCol] = $betDetails[$calculatedAtCol];
                break;
            case BetStatusEnum::Won->name:
                $betDetails[$winAmountCol] = $betDetails[$amountCol] * $betDetails[$oddsCol];
                $betDetails[$paidAtCol] = $betDetails[$calculatedAtCol];
                break;
            case BetStatusEnum::Returned->name:
                $betDetails[$winAmountCol] = $betDetails[$amountCol];
                $betDetails[$paidAtCol] = $betDetails[$calculatedAtCol];
                break;
            case BetStatusEnum::CashedOut->name:
                $betDetails[$winAmountCol] =  $betDetails[$amountCol];
                $betDetails[$paidAtCol] = $betDetails[$calculatedAtCol];
                break;
            case BetStatusEnum::Lost->name:
                $betDetails[$winAmountCol] = 0;
                $betDetails[$paidAtCol] = null;
                break;

            default:
                return $this->makeBetDetails();
                break;
        }

        return $betDetails;
    }

    /**
     * Create bet selections
     *
     * @param  mixed $bet
     * @return void
     */
    private function createBetSelections(Bet $bet): void
    {
        $context = $bet[TableEnum::Context->dbName()];

        if ($context == BetContextEnum::Sport->name) {

            $betType = $bet[TableEnum::BetType->dbName()];

            $count = match ($betType) {

                BetTypeEnum::Singel->name => 1,
                BetTypeEnum::Multiple->name => rand(2, 5),
                BetTypeEnum::System->name => rand(2, 5),

                default => 1
            };

            BetSelection::factory($count)
                ->create([
                    BetSelectionsTableEnum::BetId->dbName() => $bet->id,
                ]);
        }
    }
}
