<?PHP

namespace App\HHH_Library\Calendar\CalendarTypes;

class PersianDate
{


    /**
     * Jalali date
     *
     * @param  string $format Exp: "Y/m/d H:i:s"
     * @param  int|false $timestamp false: Returns the current datetime (now)
     * @return string
     */
    public static function jdate(string $format = "Y/m/d H:i:s", int|false $timestamp = false): string
    {

        if ($timestamp === false) {
            list($jyear, $jmonth, $jday) = self::gregorianToJalali(date("Y"), date("m"), date("d"));
            $timestamp = self::jMakeTime(date("h"), date("i"), date("s"), $jyear, $jmonth, $jday);
        }


        $year = date("Y", $timestamp);
        $month = date("m", $timestamp);
        $day = date("d", $timestamp);
        list($jyear, $jmonth, $jday) = self::gregorianToJalali($year, $month, $day);


        $a = date("a", $timestamp);
        if ($a == "pm") {
            $a = trans('JalaliDate.date.pm');
            $a2 = trans('JalaliDate.date.pm_long');
        } else {
            $a = trans('JalaliDate.date.am');
            $a2 = trans('JalaliDate.date.am_long');
        }

        if ($jday < 10) $d = "0" . $jday;
        else $d = $jday;

        $d2 = trans('JalaliDate.date.' . date("D", $timestamp));
        $j = $jday;
        $l = trans('JalaliDate.date.' . date("l", $timestamp));

        if ($jmonth < 10) $m = "0" . $jmonth;
        else $m = $jmonth;

        $f = trans('JalaliDate.date.month.' . $jmonth);
        $n = $jmonth;
        $s = trans('JalaliDate.date.S');

        // $r = $lang['date'][date("r", $timestamp)];
        $r = date("r", $timestamp);

        $t = self::jLastDay($day, $month, $year);

        $w = date("W", $timestamp + 172800);
        if ($w > 12) $w = $w - 12;
        else $w += 40;
        if ($w < 10) $w = "0" . $w;

        $y = substr($jyear, 2, 4);
        $y2 = $jyear;

        $find    = array("a", "A", "d", "D", "F", "j", "l", "m", "M", "n", "r", "S", "t", "W", "y", "Y");
        $replace = array($a, $a2, $d, $d2, $f, $j, $l, $m, $f, $n, $r, $s, $t, $w, $y, $y2);

        $output = date(str_replace($find, $replace, $format), $timestamp);

        return $output;
    }

    /**
     * Make Jalali time
     *
     * @param  string|false $hour
     * @param  string|false $minute
     * @param  string|false $second
     * @param  string|false $jyear Jalali Year
     * @param  string|false $jmonth Jalali Month
     * @param  string|false $jday Jalali Day
     * @return int|false
     */
    public static function jMakeTime(string|false $hour, string|false $minute, string|false $second, string|false $jyear, string|false $jmonth, string|false $jday): int|false
    {
        list($year, $month, $day) = self::jalaliToGregorian($jyear, $jmonth, $jday);
        return  mktime($hour, $minute, $second, $month, $day, $year);
    }

    /**
     * Find beginning day name of Jalali month
     *
     * @param  string|false $year Gregorian
     * @param  string|false $month Gregorian
     * @param  string|false $day Gregorian
     * @return string|false
     */
    public static function jMonthStartDay(string|false $year, string|false $month, string|false $day): string|false
    {
        list($jyear, $jmonth, $jday) = self::gregorianToJalali($year, $month, $day);
        list($year, $month, $day) = self::jalaliToGregorian($jyear, $jmonth, "1");

        $timestamp = mktime(0, 0, 0, $month, $day, $year);

        return date("D", $timestamp);
    }

    /**
     * Find Number Of Days In This Month
     *
     * @param  string|false $day Gregorian
     * @param  string|false $month Gregorian
     * @param  string|false $year Gregorian
     * @return int
     */
    public static function jLastDay(string|false $day, string|false $month, string|false $year): int
    {
        $lastday_gregorian = date("d", mktime(0, 0, 0, $month + 1, 0, $year));
        list($jyear, $jmonth, $jday) = self::gregorianToJalali($year, $month, $day);

        $lastdate_jalali = $jday;
        $jday2 = $jday;

        while ($jday2 >= "1") {

            if ($day < $lastday_gregorian) {

                $day++;
                list($jyear, $jmonth, $jday2) = self::gregorianToJalali($year, $month, $day);
                if ($jday2 == "1") break;
                if ($jday2 != "1") $lastdate_jalali += 1;
            } else {

                $day = 0;
                $month += 1;
                if ($month == 13) {
                    $month = "1";
                    $year++;
                }
            }
        }
        return $lastdate_jalali;
    }

    /**
     * Convert number to Farsi digits
     *
     * @param  string $string
     * @return string
     */
    public static function Convertnumber2farsi(string $string): string
    {
        $find = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $replace = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹");

        return str_replace($find, $replace, $string);
    }

    /**
     * Jalali Division
     *
     * @param  int $divisible
     * @param  int $divider
     * @return int
     */
    public static function jDiv(int $divisible, int $divider): int
    {
        return (int) ($divisible / $divider);
    }

    /**
     * Gregorian date To Jalali date
     *
     * @param  string|int|false $g_y Gregorian Year
     * @param  string|int|false $g_m Gregorian Month
     * @param  string|int|false $g_d Gregorian Day
     * @return array
     */
    public static function gregorianToJalali(string|int|false $g_y, string|int|false $g_m, string|int|false $g_d): array
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        $gy = $g_y - 1600;
        $gm = $g_m - 1;
        $gd = $g_d - 1;

        $g_day_no = 365 * $gy + self::jDiv($gy + 3, 4) - self::jDiv($gy + 99, 100) + self::jDiv($gy + 399, 400);

        for ($i = 0; $i < $gm; ++$i)
            $g_day_no += $g_days_in_month[$i];
        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) /* leap and after Feb */
            $g_day_no++;
        $g_day_no += $gd;

        $j_day_no = $g_day_no - 79;

        $j_np = self::jDiv($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */
        $j_day_no = $j_day_no % 12053;

        $jy = 979 + 33 * $j_np + 4 * self::jDiv($j_day_no, 1461); /* 1461 = 365*4 + 4/4 */

        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::jDiv($j_day_no - 1, 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
            $j_day_no -= $j_days_in_month[$i];

        $jm = $i + 1;
        $jd = $j_day_no + 1;

        return array($jy, $jm, $jd);
    }

    /**
     * Jalali date To Gregorian date
     *
     * @param  string|int|false $j_y Jalali Year
     * @param  string|int|false $j_m Jalali Month
     * @param  string|int|false $j_d Jalali Day
     * @return array
     */
    public static function jalaliToGregorian(string|int|false $j_y, string|int|false $j_m, string|int|false $j_d): array
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        $jy = $j_y - 979;
        $jm = $j_m - 1;
        $jd = $j_d - 1;

        $j_day_no = 365 * $jy + self::jDiv($jy, 33) * 8 + self::jDiv($jy % 33 + 3, 4);
        for ($i = 0; $i < $jm; ++$i)
            $j_day_no += $j_days_in_month[$i];

        $j_day_no += $jd;

        $g_day_no = $j_day_no + 79;

        $gy = 1600 + 400 * self::jDiv($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) { /* 36525 = 365*100 + 100/4 */
            $g_day_no--;
            $gy += 100 * self::jDiv($g_day_no, 36524); /* 36524 = 365*100 + 100/4 - 100/100 */
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365)
                $g_day_no++;
            else
                $leap = false;
        }

        $gy += 4 * self::jDiv($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;

            $g_day_no--;
            $gy += self::jDiv($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
        $gm = $i + 1;
        $gd = $g_day_no + 1;

        return array($gy, $gm, $gd);
    }

    /**
     * This function converts incomplete dates to full dates
     * and converts that to timestamp.
     *
     * Example:
     *  1402 => 1402/01/01 00:00:00
     *  1402/03 => 1402/03/01  00:00:00
     *  1402/03/12 => 1402/03/12  00:00:00
     *  1402/03/12 20 => 1402/03/12  20:00:00
     *  1402/03/12 20:36 => 1402/03/12  20:36:00
     *  1402/03/12 20:36:05 => 1402/03/12  20:36:05
     *
     * @param  string $datetime
     * @return int|false
     */
    public static function jstrtotime(string $datetime): int|false
    {

        $datetimeArray = explode(" ", trim($datetime));

        $date = reset($datetimeArray);
        // $date = preg_split('/-/', $date);return $date;
        $date = preg_split('/[\/-]/', $date); //split both date format: 1399/10/25 OR 1399-10-25
        if (!isset($date[0]) || !intval($date[0])) $date[0] = self::jdate("Y");
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

        $time = self::jMakeTime($time[0], $time[1], $time[2], $date[0], $date[1], $date[2]);

        return intval($time) ? $time : false;
    }
}
