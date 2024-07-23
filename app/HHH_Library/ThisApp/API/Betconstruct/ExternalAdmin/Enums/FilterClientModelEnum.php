<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Traits\FilterableEnum;
use App\Interfaces\Castable;

enum FilterClientModelEnum implements Castable
{
    use EnumCastParams;
    use FilterableEnum;

    case Id; // int? Client Id
    case RequestHash; //  string required for partner verification
    case RegistrationFromStamp; //  long?
    case RegistrationToStamp; //  long?
    case NickName; //  string
    case FirstName; //  string
    case LastName; //  string
    case MiddleName; //  string
    case Gender; //  int? Male = 1, Female = 2
    case BirthDateStamp; //  long?
    case DocumentNumber; //  string
    case PersonalId; //  string
    case Email; //  string
    case Phone; //  sting
    case MobilePhone; //  string
    case IsLocked; //  bool?
    case RegionCode; //  string
    case City; //  string
    case Login; //  string
    case CurrencyId; //  string
    case ExternalId; //  string
    case IsWithRestrictions; // bool If true client will be returned with the restrictions params (CanLogin,CanBet,CanDeposit,CanWithdraw)


    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        /**
         * Default cast is string,
         * so only register non string cases
         */

        /** @var CastEnum $castEnum */
        $castEnum = match ($this) {

            self::Id, self::Gender => CastEnum::Int,
            self::IsLocked, self::IsWithRestrictions => CastEnum::Boolean,
            self::RegistrationFromStamp, self::RegistrationToStamp, self::BirthDateStamp => CastEnum::Float,

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }
}
