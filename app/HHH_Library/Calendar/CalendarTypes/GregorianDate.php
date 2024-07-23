<?PHP

namespace App\HHH_Library\Calendar\CalendarTypes;

class GregorianDate
{

    /**
     * This function converts incomplete dates to full dates
     * and converts that to timestamp.
     *
     * Example:
     *  2021 => 2021-01-01 00:00:00
     *  2021-03 => 2021-03-01  00:00:00
     *  2021-03-12 => 2021-03-12  00:00:00
     *  2021-03-12 20 => 2021-03-12  20:00:00
     *  2021-03-12 20:36 => 2021-03-12  20:36:00
     *  2021-03-12 20:36:05 => 2021-03-12  20:36:05
     *
     * @param  string $datetime
     * @return int|false
     */
    public static function strtotime(string $datetime): int|false
    {
        $datetimeArray = explode(" ", trim($datetime));

        $date = reset($datetimeArray);
        // $date = preg_split('/-/', $date);return $date;
        $date = preg_split('/[\/-]/', $date); //split both date format: 2021/01/20 OR 2021-01-20
        if (!isset($date[0]) || !intval($date[0])) $date[0] = date("Y");
        if (!isset($date[1]) || !intval($date[1])) $date[1] = "01";
        if (!isset($date[2]) || !intval($date[2])) $date[2] = "01";

        if (isset($datetimeArray[1])) {
            $time = end($datetimeArray);
            $time = explode(":", $time);
        } else
            $time = array();

        if (!isset($time[0]) || !intval($time[0])) $time[0] = "00";
        if (!isset($time[1]) || !intval($time[1])) $time[1] = "00";
        if (!isset($time[2]) || !intval($time[2])) $time[2] = "00";

        $time = self::maketime($time[0], $time[1], $time[2], $date[0], $date[1], $date[2]);

        return intval($time) ? $time : false;
    }

    /**
     * maketime
     * Overrided for prevent confuse
     *
     * @param  int $hour
     * @param  int $minute
     * @param  int $second
     * @param  int $year
     * @param  int $month
     * @param  int $day
     * @return int
     */
    public static function maketime(int $hour, int $minute, int $second, int $year, int $month, int $day): int|false
    {
        return mktime($hour, $minute, $second, $month, $day, $year);
    }
}
