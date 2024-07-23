<?php

namespace App\Enums\General;

use App\Enums\Database\Tables\CurrencyRatesTableEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;
use App\Models\BackOffice\Currencies\CurrencyRate;

enum CurrencyEnum implements Translatable
{
    use EnumActions;


    case USD;
    case IRR;
    case TOM;
    case IRT;

    /**
     * Get fixed rates items
     *
     * @param  bool $returnNames
     * @return array
     */
    public static function fixedRateItems(bool $returnNames = false): array
    {
        $fixedRateItems =  [
            self::USD, // It's base currency and no need to convert

            self::TOM, // = self::IRR /10
            self::IRT, // = self::IRR /10000
        ];

        if (!$returnNames)
            return $fixedRateItems;

        $names = [];
        foreach ($fixedRateItems as $case) {

            array_push($names, $case->name);
        }

        return $names;
    }

    /**
     * Get dynamic rates items
     *
     * @param  bool $returnNames
     * @return array
     */
    public static function dynamicRateItems(bool $returnNames = false): array
    {
        $allCases = self::cases();
        $fixedRateNames = self::fixedRateItems(true);

        $dynamicRateItems = [];
        foreach ($allCases as $case) {

            if (!in_array($case->name, $fixedRateNames)) {

                array_push($dynamicRateItems, $returnNames ? $case->name : $case);
            }
        }

        return $dynamicRateItems;
    }

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        $translate = __('currency.name.' . $this->name, [], is_null($locale) ? null : $locale->value);
        return sprintf("%s (%s)", $translate, $this->name);
    }

    /**
     * Get item display string without ISO code
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translateWithoutIso(LocaleEnum $locale = null): ?string
    {
        return __('currency.name.' . $this->name, [], is_null($locale) ? null : $locale->value);
    }

    /**
     * Get collection list
     * for group checkboxes or dropdown list
     *
     * @param bool $useReverseList
     * @param bool $ascSort
     * @param bool $sortText
     * @return array
     */
    public static function getCollectionList(bool $useReverseList = false, bool $ascSort = true, bool $sortText = true): array
    {
        $collectionList =  DropdownListCreater::makeByArray(self::translatedArray());

        if ($useReverseList)
            $collectionList->useReverseList();

        return $collectionList->sort($ascSort, $sortText)->get();
    }

    /**
     * Exchange amount of this currency to other currency
     *
     * @param  float $amount
     * @param  self $toCurrency
     * @return ?float
     */
    public function exchange(float $amount, self $toCurrency): ?float
    {
        if ($amount == 0)
            return 0;

        $exchange = match ($this) {

            self::IRR => $this->exchangeIrr($amount, $toCurrency),
            self::TOM => $this->exchangeTom($amount, $toCurrency),
            self::IRT => $this->exchangeIrt($amount, $toCurrency),
            self::USD => $this->exchangeUsd($amount, $toCurrency),

            default => null
        };

        return is_null($exchange) ? null : round($exchange, 2);
    }

    /**
     * Get one USD dynamic rate
     *
     * @param  self $currency
     * @return ?float
     */
    private function getOneUsdDynamicRate(self $currency): ?float
    {
        if ($currencyRate = CurrencyRate::where(CurrencyRatesTableEnum::NameIso->dbName(), $currency->name)->first()) {
            $oneUsdRate = $currencyRate[CurrencyRatesTableEnum::OneUsdRate->dbName()];

            return is_null($oneUsdRate) ? null : $oneUsdRate;
        }

        return null;
    }

    /**
     * Exchange IRR currency to other currencies
     *
     * @param  float $amount
     * @param  self $toCurrency
     * @return ?float
     */
    private function exchangeIrr(float $amount, self $toCurrency): ?float
    {
        $oneUsdRate = $this->getOneUsdDynamicRate($this);

        return match ($toCurrency) {

            self::IRR => $amount,
            self::TOM => $amount / 10,
            self::IRT => $amount / 10000,
            self::USD => $amount / $oneUsdRate,
            self::USD => is_null($oneUsdRate) ? null : $amount / $oneUsdRate,

            default => null
        };
    }

    /**
     * Exchange TOM currency to other currencies
     *
     * @param  float $amount
     * @param  self $toCurrency
     * @return ?float
     */
    private function exchangeTom(float $amount, self $toCurrency): ?float
    {
        $amountIrr = $amount * 10;
        return self::IRR->exchange($amountIrr, $toCurrency);
    }

    /**
     * Exchange IRT currency to other currencies
     *
     * @param  float $amount
     * @param  self $toCurrency
     * @return ?float
     */
    private function exchangeIrt(float $amount, self $toCurrency): ?float
    {
        $amountIrr = $amount * 10000;
        return self::IRR->exchange($amountIrr, $toCurrency);
    }

    /**
     * Exchange USD currency to other currencies
     *
     * @param  float $amount
     * @param  self $toCurrency
     * @return ?float
     */
    private function exchangeUsd(float $amount, self $toCurrency): ?float
    {
        $rateUsdIrr = $this->getOneUsdDynamicRate(self::IRR);

        return match ($toCurrency) {

            self::IRR => is_null($rateUsdIrr) ? null : $amount * $rateUsdIrr,
            self::TOM => is_null($rateUsdIrr) ? null : ($amount * $rateUsdIrr) / 10,
            self::IRT => is_null($rateUsdIrr) ? null : ($amount * $rateUsdIrr) / 10000,
            self::USD => $amount,

            default => null
        };
    }
}
