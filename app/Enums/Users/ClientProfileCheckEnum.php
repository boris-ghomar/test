<?php

namespace App\Enums\Users;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\GendersEnum as ExternarAdminGendersEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

enum ClientProfileCheckEnum
{
    use EnumActions;

    case ProfileRequiredItems;
    case FurtherInformationTab;
    case EmailTab;
    case LastEmailVerification;

    /**
     * Check if case completed
     *
     * @param  \App\Models\User $user
     * @return bool
     */
    public function isCompleted(?User $user): bool
    {
        if (is_null($user))
            $user = User::authUser();

        if (!$user->isClient())
            return false;

        return match ($this) {
            self::ProfileRequiredItems  => $this->isProfileRequiredItemsCompleted($user),
            self::FurtherInformationTab => $this->isFurtherInformationTabCompleted($user),
            self::EmailTab              => $this->isEmailTabCompleted($user),
            self::LastEmailVerification => $this->isLastEmailVerified($user),

            default => false,
        };
    }

    /**
     * Get search query to find completed case
     *
     * NOTICE:
     * The query may need to join the table to the table you want,
     * check before using it.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param bool $passed true: will include passed records, false: will include not passed records
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function isCompletedSearchQuery(Builder $query, bool $passed): Builder
    {
        $usersTable = DatabaseTablesEnum::Users;
        $betconstructClientsTable = DatabaseTablesEnum::BetconstructClients;

        // ProfileRequiredItems
        if ($this == self::ProfileRequiredItems) {
            return self::FurtherInformationTab->isCompletedSearchQuery($query, $passed);
        }
        // FurtherInformationTab
        else if ($this == self::FurtherInformationTab) {

            if ($passed) {
                return $query->whereIn(ClientModelEnum::Gender->dbNameWithTable($betconstructClientsTable), [ExternarAdminGendersEnum::Male->value, ExternarAdminGendersEnum::Female->value])
                    ->whereNotNull(ClientModelEnum::ProvinceInternal->dbNameWithTable($betconstructClientsTable))
                    ->whereNotNull(ClientModelEnum::CityInternal->dbNameWithTable($betconstructClientsTable))
                    ->whereNotNull(ClientModelEnum::ContactNumbersInternal->dbNameWithTable($betconstructClientsTable))
                    ->whereNotNull(ClientModelEnum::ContactMethodsInternal->dbNameWithTable($betconstructClientsTable))
                    ->whereNotNull(ClientModelEnum::CallerGenderInternal->dbNameWithTable($betconstructClientsTable));
            } else {

                return $query->where(function ($query) use ($betconstructClientsTable) {
                    return $query->whereNotIn(ClientModelEnum::Gender->dbNameWithTable($betconstructClientsTable), [ExternarAdminGendersEnum::Male->value, ExternarAdminGendersEnum::Female->value])
                        ->orWhereNull(ClientModelEnum::ProvinceInternal->dbNameWithTable($betconstructClientsTable))
                        ->orWhereNull(ClientModelEnum::CityInternal->dbNameWithTable($betconstructClientsTable))
                        ->orWhereNull(ClientModelEnum::ContactNumbersInternal->dbNameWithTable($betconstructClientsTable))
                        ->orWhereNull(ClientModelEnum::ContactMethodsInternal->dbNameWithTable($betconstructClientsTable))
                        ->orWhereNull(ClientModelEnum::CallerGenderInternal->dbNameWithTable($betconstructClientsTable));
                });
            }
        }
        // EmailTab
        else if ($this == self::EmailTab) {
            return ($passed) ?
                $query->whereNotNull(ClientModelEnum::Email->dbNameWithTable($betconstructClientsTable))
                : $query->whereNull(ClientModelEnum::Email->dbNameWithTable($betconstructClientsTable));
        }
        // LastEmailVerification
        else if ($this == self::LastEmailVerification) {

            return ($passed) ?
                $query->whereNotNull(UsersTableEnum::EmailVerifiedAt->dbNameWithTable($usersTable))
                : $query->whereNull(UsersTableEnum::EmailVerifiedAt->dbNameWithTable($usersTable));
        }

        return $query;
    }

    /**
     * Check if client's profile required items has been completed
     *
     * @param  \App\Models\User $user
     * @return bool
     */
    private function isProfileRequiredItemsCompleted(User $user): bool
    {
        return $this->isFurtherInformationTabCompleted($user);
    }

    /**
     * Check if client's further information has been completed
     *
     * @param  \App\Models\User $user
     * @return bool
     */
    private function isFurtherInformationTabCompleted(User $user): bool
    {
        $betconstructClient = $user->betconstructClient;

        if (is_null($betconstructClient))
            Auth::logout();

        $attr = $betconstructClient[ClientModelEnum::Gender->dbName()];
        if (!in_array($attr, [ExternarAdminGendersEnum::Male->value, ExternarAdminGendersEnum::Female->value]))
            return false;

        $attr = $betconstructClient[ClientModelEnum::ProvinceInternal->dbName()];
        if (empty($attr))
            return false;

        $attr = $betconstructClient[ClientModelEnum::CityInternal->dbName()];
        if (empty($attr))
            return false;

        $attr = $betconstructClient[ClientModelEnum::ContactNumbersInternal->dbName()];
        if (empty($attr))
            return false;

        $attr = $betconstructClient[ClientModelEnum::ContactMethodsInternal->dbName()];
        if (empty($attr))
            return false;

        $attr = $betconstructClient[ClientModelEnum::CallerGenderInternal->dbName()];
        if (empty($attr))
            return false;

        return true;
    }

    /**
     * Check if client's email tab has been completed
     *
     * @param  \App\Models\User $user
     * @return bool
     */
    private function isEmailTabCompleted(User $user): bool
    {
        return $this->isLastEmailVerified($user);
    }

    /**
     * Check if last email already verified
     *
     * @param  \App\Models\User $user
     * @return bool
     */
    private function isLastEmailVerified(User $user): bool
    {
        return !is_null($user[UsersTableEnum::EmailVerifiedAt->dbName()]);
    }
}
