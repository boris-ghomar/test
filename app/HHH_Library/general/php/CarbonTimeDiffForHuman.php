<?php

namespace App\HHH_Library\general\php;

use Carbon\Carbon;

/**
 * NOTICE:
 *
 * This class is incomplete and is not used anywhere in the program
 * and must be completed when needed.
 */
class CarbonTimeDiffForHuman
{

    private $sourceDate;
    private $comparableDate;

    private $positiveSuffix;
    private $negativeSuffix;

    private $useYear = true;
    private $useMonth = true;
    private $useDay = true;
    private $useHour = true;
    private $useMinute = true;
    private $useSecond = true;


    /**
     * __construct
     *
     * @param  mixed $sourceDate
     * @param  mixed $comparableDate
     * @return void
     */
    function __construct(mixed $sourceDate, mixed $comparableDate)
    {

        $this->sourceDate = Carbon::createFromDate($sourceDate);
        $this->comparableDate = Carbon::createFromDate($comparableDate);

        $this->setPositiveSuffix(__('general.TimeDisplay.after'));
        $this->setNegativeSuffix(__('general.TimeDisplay.ago'));
    }

    /**
     * Get the difference as the carbon standard
     *
     * @return mixed
     */
    public function getCarbonDiff(): mixed
    {
        return $this->sourceDate->diff($this->comparableDate);
    }

    /**
     * Check if the source date passed by comparable date
     *
     * @return bool
     */
    public function isSourceDatePassed(): bool
    {
        /**
         * (sourceDate < comparableDate) ? invert = 1 : invert = 0
         */
        return $this->sourceDate < $this->comparableDate ? true : false;
    }

    /**
     * Get time difference for human
     *
     * @return string
     */
    public function getDiff(): string
    {
        $diff = $this->getCarbonDiff();

        $res = [];

        /**
         * (sourceDate < comparableDate) ? invert = 1 : invert = 0
         */
        $invert = $diff->invert;

        $year = $diff->y;
        $month = $diff->m;
        $day = $diff->d;

        $hour = $diff->h;
        $minute = $diff->i;
        $second = $diff->s;

        if ($year > 0 && $this->useYear)
            array_push($res, trans_choice('general.TimeDisplay.year', $year, ['value' => $year]));
        if ($month > 0 && $this->useMonth)
            array_push($res, trans_choice('general.TimeDisplay.month', $month, ['value' => $month]));
        if ($day > 0 && $this->useDay)
            array_push($res, trans_choice('general.TimeDisplay.day', $day, ['value' => $day]));

        if ($hour > 0 && $this->useHour)
            array_push($res, trans_choice('general.TimeDisplay.hour', $hour, ['value' => $hour]));
        if ($minute > 0 && $this->useMinute)
            array_push($res, trans_choice('general.TimeDisplay.minute', $minute, ['value' => $minute]));
        if ($second > 0 && $this->useSecond)
            array_push($res, trans_choice('general.TimeDisplay.second', $second, ['value' => $second]));

        $dateDiff = implode(__('general.TimeDisplay.and'), $res);

        if ($invert && !empty($this->negativeSuffix))
            $dateDiff .= " " . $this->negativeSuffix;

        if (!$invert && !empty($this->positiveSuffix))
            $dateDiff .= " " . $this->positiveSuffix;

        return $dateDiff;
    }

    /**
     * Set positive suffix
     *
     * @param  ?string $suffix
     * @return self
     */
    public function setPositiveSuffix(?string $suffix): self
    {
        $this->positiveSuffix = is_null($suffix) ? '' : $suffix;
        return $this;
    }

    /**
     * Set negative suffix
     *
     * @param  ?string $suffix
     * @return self
     */
    public function setNegativeSuffix(?string $suffix): self
    {
        $this->negativeSuffix = is_null($suffix) ? '' : $suffix;
        return $this;
    }

    /**
     * Ignore year
     *
     * @return self
     */
    public function ignoreSuffixes(): self
    {
        $this->setPositiveSuffix(null);
        $this->setNegativeSuffix(null);
        return $this;
    }

    /**
     * Ignore year
     *
     * @return self
     */
    public function ignoreYear(): self
    {
        $this->useYear = false;
        return $this;
    }

    /**
     * Ignore month
     *
     * @return self
     */
    public function ignoreMonth(): self
    {
        $this->useMonth = false;
        return $this;
    }

    /**
     * Ignore day
     *
     * @return self
     */
    public function ignoreDay(): self
    {
        $this->useDay = false;
        return $this;
    }

    /**
     * Ignore hour
     *
     * @return self
     */
    public function ignoreHour(): self
    {
        $this->useHour = false;
        return $this;
    }

    /**
     * Ignore minute
     *
     * @return self
     */
    public function ignoreMinute(): self
    {
        $this->useMinute = false;
        return $this;
    }

    /**
     * Ignore second
     *
     * @return self
     */
    public function ignoreSecond(): self
    {
        $this->useSecond = false;
        return $this;
    }
}
