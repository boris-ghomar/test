<?php

namespace App\HHH_Library\general\php;


class JsonHelper
{

    /**
     * Check whether the input is json or not
     *
     * @param mixed $string
     * @return bool
     */
    public static function isJson(mixed $string): bool
    {
        if (!is_string($string))
            return false;

        if ($string === null || trim($string) == "")
            return false;


        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Encode json encode as unicode
     *
     * @param  array $data
     * @return string
     */
    public static function jsonEncode_UNESCAPED_UNICODE(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
