<?php

namespace App\HHH_Library\general\php;


/**
 * NOTICE:
 *
 * This class is incomplete and is not used anywhere in the program
 * and must be completed when needed.
 */
class TimeDisplay
{

    public static function baseOnSecond(int $seconds)
    {

        if ($seconds < 60) {
            return self::second($seconds);
        } else if ($seconds < 60 * 60) {

            if ($seconds % 60 == 0)
                return self::minute($seconds / 60);
            else
                return sprintf("%s, %s", self::minute($seconds / 60), self::second($seconds % 60));
        } else if ($seconds < 24 * 3600) {
        }
    }

    public static function second(int $seconds)
    {
        if ($seconds <= 0)
            return "";
        else if ($seconds < 60) {
            return trans('general.TimeDisplay.second', ['value' => $seconds]);
        }
    }

    public static function minute(int $minutes)
    {
        if ($minutes <= 0)
            return "";
        else if ($minutes < 60) {
            return trans('general.TimeDisplay.minute', ['value' => $minutes]);
        }
    }

    public static function hour(int $hours)
    {
        if ($hours <= 0)
            return "";
        else if ($hours < 60) {
            return trans('general.TimeDisplay.hour', ['value' => $hours]);
        }
    }
}
