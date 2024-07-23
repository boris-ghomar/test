<?php

namespace App\Enums\Users;

use App\Enums\General\PartnerEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum UsersTypesEnum
{
    use EnumActions;

    case Personnel;
    case BetconstructClient;

    /**
     * Get the partner of user type
     *
     * @return null|\App\Enums\General\PartnerEnum
     */
    public function getPartner(): ?PartnerEnum
    {
        return match ($this) {
            self::Personnel             => null,
            self::BetconstructClient    => PartnerEnum::Betconstruct,

            default => null
        };
    }

    /**
     * Get the partner of user type by case name
     *
     * @param null|string $typeName
     * @return null|\App\Enums\General\PartnerEnum
     */
    public static function getPartnerByCaseName(?string $caseName): ?PartnerEnum
    {
        if (empty($caseName))
            return null;

        if ($case = self::getCase($caseName)) {

            return $case->getPartner();
        }

        return null;
    }
}
