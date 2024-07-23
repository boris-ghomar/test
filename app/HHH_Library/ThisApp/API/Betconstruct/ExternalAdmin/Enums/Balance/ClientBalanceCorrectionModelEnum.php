<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum ClientBalanceCorrectionModelEnum implements Castable
{
    use EnumCastParams;
    use EnumToDatabaseColumnName;

    case ClientId; // int Client Id
    case CurrencyId; // string
    case PaymentSystemId; // int? on this stage not need to provide any values
    case Amount; // decimal correction amount
    case Info; // string info about correction
    case DocumentType; // DocumentTypeEnum document type that need to be created for now only(correctionUp and correctionDown CorrectionUp = 301, CorrectionDown = 302, BonusCorrection=303) (Stored in \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance\BalanceCorrectionTypeEnum)
    case DocumentId; // long? used to return our resulted document Id not need to fill any value
    case ExternalId; // string
    case RequestHash; // string required for partner verification


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

            self::ClientId, self::PaymentSystemId, self::DocumentType, self::DocumentId
            => CastEnum::Int,

            self::Amount => CastEnum::Float,

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }
}
