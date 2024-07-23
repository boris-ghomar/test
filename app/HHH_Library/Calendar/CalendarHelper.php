<?PHP

namespace App\HHH_Library\Calendar;

use App\HHH_Library\Calendar\CalendarTypes\GregorianDate;
use App\HHH_Library\Calendar\CalendarTypes\PersianDate;
use App\HHH_Library\general\php\Enums\CalendarTypeEnum;
use App\HHH_Library\general\php\Enums\PregPatternValidationEnum;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Illuminate\Support\Str;


class CalendarHelper
{

    protected bool $timestampModeStatus = false;
    protected string $format = "";
    protected string $timezone;
    protected CalendarTypeEnum $calendarType;

    /**
     * Constructor
     *
     * @param  ?string $timezone
     * @param  \App\HHH_Library\general\php\Enums\CalendarTypeEnum|null $calendarType
     * @return void
     */
    public function __construct(?string $timezone = "+00:00", ?CalendarTypeEnum $calendarType = CalendarTypeEnum::Gregorian)
    {
        $this->setTimezone($timezone);
        $this->setCalendarType($calendarType);
    }

    /************************* Setter & Getter ***************************/

    /**
     * Is timestamp mode enable?
     *
     * If this mode is enabled, entries that are
     * considered timestamps will be converted to date.
     *
     * Attention:
     * Inputs like "2023" are time stamps.
     * Leave this item disable if you don't really need it.
     *
     * @return bool
     */
    public function isTimestampModeEnable(): bool
    {
        return $this->timestampModeStatus;
    }

    /**
     * Set timestamp mode status
     *
     * If this mode is enabled, entries that are
     * considered timestamps will be converted to date.
     *
     * Attention:
     * Inputs like "2023" are time stamps.
     * Leave this item disable if you don't really need it.
     *
     * @param  bool $status true ? enable : disable
     * @return void
     */
    public function setTimestampModeStatus(bool $status): void
    {
        $this->timestampModeStatus = $status;
    }

    /**
     * Set date-time format
     *
     * @param  ?string $timezone
     * @return self
     */
    public function setFormat(?string $format): self
    {
        if (!empty($format))
            $this->format = $format;

        return $this;
    }

    /**
     * Get date-time format
     *
     * @param bool $convertToUTC
     * @return string
     */
    public function getFormat(bool $convertToUTC): string
    {
        if (!empty($this->format))
            return $this->format;

        return $convertToUTC ? CalendarTypeEnum::Gregorian->defaultDateTimeFormat() : $this->getCalendarType()->defaultDateTimeFormat();
    }

    /**
     * Set timezone
     *
     * @param  ?string $timezone
     * @return self
     */
    public function setTimezone(?string $timezone): self
    {
        $this->timezone = PregPatternValidationEnum::Timezone->validate($timezone) ? $timezone : "+00:00";
        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * Set calendar type
     *
     * @param  \App\HHH_Library\general\php\Enums\CalendarTypeEnum|null $calendarType
     * @return self
     */
    public function setCalendarType(CalendarTypeEnum|null $calendarType): self
    {
        $this->calendarType = !is_null($calendarType) ? $calendarType : CalendarTypeEnum::Gregorian;
        return $this;
    }

    /**
     * Get calendar type
     *
     * @return \App\HHH_Library\general\php\Enums\CalendarTypeEnum
     */
    public function getCalendarType(): CalendarTypeEnum
    {
        return $this->calendarType;
    }
    /************************* Setter & Getter END ***************************/

    /**
     * This function converts UTC input date-time
     * to local time and selected calendar type
     *
     * @param  ?string  $UTC_Datetime [DateTimeString]
     * @return ?string [DateTimeString]
     */
    public function convertToLocalDate(?string $UTC_Datetime): ?string
    {
        if (empty($UTC_Datetime))
            return null;

        if ($this->isTimestampModeEnable()) {
            // Check timestamp input like as "1541843467"
            $UTC_Datetime = self::converTimstampToDateString($UTC_Datetime);
            if (is_null($UTC_Datetime))
                return null;
        }

        try {

            $datetime = (new Carbon($UTC_Datetime))
                ->setTimezone(new CarbonTimeZone($this->getTimezone()))
                ->format($this->getFormat(false));

            return match ($this->getCalendarType()) {

                CalendarTypeEnum::Gregorian => $datetime,
                CalendarTypeEnum::Persian   => PersianDate::jdate($this->getFormat(false), strtotime($datetime)),

                default => $datetime
            };
        } catch (\Exception $e) {

            return null;
            // return 'Invalid DateTime Exception: '.$e->getMessage();
        }
    }

    /**
     * This function takes a local date and time
     * and converts it to UTC date in Gregorian date format.
     *
     * @param  ?string $localDateTime
     * @return ?string
     */
    public function convertToUTC(?string $localDateTime): ?string
    {
        if (empty($localDateTime))
            return null;

        if ($this->isTimestampModeEnable()) {
            // Check timestamp input like as "1541843467"
            $localDateTime = self::converTimstampToDateString($localDateTime);
            if (is_null($localDateTime))
                return null;
        }

        try {

            $timestamp = match ($this->getCalendarType()) {

                CalendarTypeEnum::Gregorian => GregorianDate::strtotime($localDateTime),
                CalendarTypeEnum::Persian   => PersianDate::jstrtotime($localDateTime),

                default => strtotime($localDateTime)
            };

            return (new Carbon($timestamp))
                ->setTimezone(new CarbonTimeZone($this->reverseTimezone($this->getTimezone())))
                ->format($this->getFormat(true));
        } catch (\Exception $e) {
            return null;
            // return 'Invalid DateTime Exception: '.$e->getMessage();
        }
    }

    /**
     * This function takes a timezone and inverts it.
     * For example, if it is positive, it turns it into negative
     * and if it is negative, it turns it into positive.
     *
     * Example::
     * +01:00 => -01:00
     * -03:30 => +03:30
     *  02:00 => -02:00
     *
     *
     * @param  string $timezone
     * @return string
     */
    public function reverseTimezone(string $timezone = "+00:00"): string
    {

        $timezone = trim($timezone);

        if (Str::startsWith($timezone, ['+', '-'])) {

            $reverseTimezone = (Str::startsWith($timezone, '+')) ? Str::replaceFirst('+', '-', $timezone) : Str::replaceFirst('-', '+', $timezone);
        } else
            $reverseTimezone = "-" . $timezone;

        return $reverseTimezone;
    }

    /**
     * Conver timstamp to date string, if input is timestamp
     *
     * @param  mixed $inputDate
     * @return ?string
     */
    public static function converTimstampToDateString(?string $date): ?string
    {
        try {
            // Check timestamp input like as "1541843467"
            if (self::isTimestamp($date))
                return Carbon::createFromTimestamp($date)->toDateString();
        } catch (\Throwable $th) {
            return null;
        }

        return $date;
    }

    /**
     * Check whether input date is timestamp or not.
     * sample timestamp "1541843467"
     *
     * @param  ?string $date
     * @return bool
     */
    public static function isTimestamp(?string $date): bool
    {
        try {
            if (empty($date) || !is_numeric($date))
                return false;

            if (strtotime(date('Y-m-d H:i:s', $date)) == intval($date))
                return true;
        } catch (\Throwable $th) {
        }

        return false;
    }
}
