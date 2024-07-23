<?php

namespace App\Rules\General\User;

use App\HHH_Library\general\php\Enums\PregPatternValidationEnum;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class DateTimeFormatRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = preg_replace('/\s+/', ' ', $value); // Replace extra white spaces with single space

        $value = Str::of($value)->trim();

        $userCalendarType = User::authUser()->getCalendarHelper()->getCalendarType();
        $userGuideDateFormat = $userCalendarType->userGuideDateFormat();

        if (!$value->isEmpty()) {

            $splitedDateTime = explode(" ", $value->toString());

            if (count($splitedDateTime) > 1) {
                $date = $splitedDateTime[0];
                $time = $splitedDateTime[1];
            } else {
                $date = $splitedDateTime[0];
                $time = null;
            }

            // Date pattern check
            $datePregPattern = $userCalendarType->datePregPattern();
            if (!$datePregPattern->validate($date))
                $fail('validation.custom.DateTimeFormat.IncorrectDateFormat')->translate(['correctFormat' => $userGuideDateFormat]);

            if (!is_null($time)) {
                // Time pattern check

                $timePregPattern = PregPatternValidationEnum::Hour24;

                if (!$timePregPattern->validate($time))
                    $fail('validation.custom.DateTimeFormat.IncorrectTimeFormat')->translate();
            }
        } else {

            $fail('validation.custom.DateTimeFormat.IncorrectDateFormat')->translate(['correctFormat' => $userGuideDateFormat]);
        }

    }
}
