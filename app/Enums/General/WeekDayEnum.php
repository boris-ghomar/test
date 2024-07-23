<?php

namespace App\Enums\General;

use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum WeekDayEnum implements Translatable
{
    use EnumActions;

    case Monday;
    case Tuesday;
    case Wednesday;
    case Thursday;
    case Friday;
    case Saturday;
    case Sunday;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        $name = strtolower(substr($this->name, 0, 3));

        return __('general.WeekDays.fullName.' . $name, [], is_null($locale) ? null : $locale->value);
    }

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translateShortName(LocaleEnum $locale = null): ?string
    {
        $name = strtolower(substr($this->name, 0, 3));

        return __('general.WeekDays.shortName.' . $name, [], is_null($locale) ? null : $locale->value);
    }

    /**
     * Translate list of day names
     *
     * @param  array $dayNames
     * @param  bool $returnShortName  true? shortname : fullName
     * @param  \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return array
     */
    public static function translateNameList(array $dayNames, bool $returnShortName = false, LocaleEnum $locale = null): array
    {
        $translatedList = [];

        foreach ($dayNames as $dayName) {

            $dayName = ucfirst(strtolower($dayName));

            /** @var self $case */
            $case = self::getCase($dayName);
            if (!is_null($case)) {

                $translatedName = $returnShortName ? $case->translateShortName($locale) : $case->translate($locale);

                array_push($translatedList, $translatedName);
            }
        }

        return $translatedList;
    }

    /**
     * Get collection list
     * for group checkboxes or dropdown list
     *
     * @param bool $useReverseList
     * @param bool $useShortName
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum|null $locale
     * @return array
     */
    public static function getCollectionList(bool $useReverseList = false, bool $useShortName = false, ?LocaleEnum $locale = null): array
    {
        $sortLocale = is_null($locale) ? config('app.locale') : $locale->value;

        if ($sortLocale == LocaleEnum::Persian->value) {

            // Persian week days sort
            $sortedCases = [
                self::Saturday,
                self::Sunday,
                self::Monday,
                self::Tuesday,
                self::Wednesday,
                self::Thursday,
                self::Friday,
            ];
        } else {

            // Default week days sort
            $sortedCases = [
                self::Monday,
                self::Tuesday,
                self::Wednesday,
                self::Thursday,
                self::Friday,
                self::Saturday,
                self::Sunday,
            ];
        }

        $translatedArray = [];
        foreach ($sortedCases as $case) {
            $translatedArray[$case->name] = $useShortName ? $case->translateShortName($locale) : $case->translate($locale);
        }

        $collectionList =  DropdownListCreater::makeByArray($translatedArray);

        if ($useReverseList)
            $collectionList->useReverseList();

        return $collectionList->get();
    }
}
