<?php

namespace App\Enums\Users;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Translatable;

enum ClientRegistrationAvailabelFieldsEnum: string implements Translatable
{
    use EnumActions;
    use EnumToDatabaseColumnName;

    case MobilePhone = "mobile_phone";

    case Email = "email";

        // FurtherInformation step
    case Gender = "gender";
    case BirthDateStamp = "birth_date_stamp";
    case ProvinceInternal = "province_internal";
    case CityInternal = "city_internal";
    case ContactNumbersInternal = "contact_numbers_internal";
    case ContactMethodsInternal = "contact_methods_internal";
    case CallerGenderInternal = "caller_gender_internal";


    const KEY_ITEM_CASE = 1, KEY_ITEM_NAME = 2, KEY_ITEM_VALUE = 3;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return (__('PagesContent_RegisaterationBetconstruct.form.' . $this->dbName() . '.name', [], is_null($locale) ? null : $locale->value));
    }

    /**
     * Get further information cases
     *
     * @return array
     */
    public static function getFurtherInformationItems(int $key = self::KEY_ITEM_CASE): array
    {
        $cases = [
            self::Gender,
            self::BirthDateStamp,
            self::ProvinceInternal,
            self::CityInternal,
            self::ContactNumbersInternal,
            self::ContactMethodsInternal,
            self::CallerGenderInternal,
        ];

        if ($key == self::KEY_ITEM_CASE)
            return $cases;
        else if ($key == self::KEY_ITEM_NAME || $key == self::KEY_ITEM_VALUE) {

            $res = [];
            foreach ($cases as $case) {

                $item = ($key == self::KEY_ITEM_NAME) ? $case->name : $case->value;

                array_push($res, $item);
            }

            return $res;
        }

        return [];
    }
}
