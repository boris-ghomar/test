<?php

namespace App\HHH_Library\CountriesCodes;


/**
 * @source
 *      https://datahub.io/core/country-list
 */
class CountriesNameUtiles
{

    /**
     * Get country name with 2-Digit ISO-Code
     *
     * @source
     *      https://datahub.io/core/country-list
     *      https://pkgstore.datahub.io/core/country-list/data_json/data/8c458f2d15d9f2119654b29ede6e45b8/data_json.json
     *
     * @source langCodeSource:
     *              // https://pkgstore.datahub.io/core/language-codes/language-codes-3b2_json/data/3d37ea0e5aa45a469879af23cb9b83be/language-codes-3b2_json.json
     *
     * @param  string $isoCode : Sample: "ir"
     * @return string Country Name
     */
    public static function getNameWith2DigitISO($isoCode)
    {
        $isoCode = strtoupper(trim($isoCode));

        $countriesJson = file_get_contents(__DIR__ . "/countries2DigitCodes_ISO3166-1.json");
        $countries = json_decode($countriesJson, false);

        foreach ($countries as $country) {

            if ($country->Code == $isoCode)
                return $country->Name;
        }

        return null;
    }
}
