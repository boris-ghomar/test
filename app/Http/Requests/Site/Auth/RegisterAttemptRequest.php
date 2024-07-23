<?php

namespace App\Http\Requests\Site\Auth;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Session\GeneralSessionsEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Enums\Users\UsersTypesEnum;
use App\HHH_Library\Calendar\CalendarHelper;
use App\HHH_Library\general\php\ArrayHelper;
use App\HHH_Library\general\php\Enums\CalendarTypeEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\GendersEnum as ExternalAdminGendersEnum;
use App\Http\Controllers\Site\Auth\Betconstruct\RegisterBetconstructController;
use App\Interfaces\Request\TabPageRequestInterface;
use App\Models\User;
use App\Rules\General\Database\UniqueSuperKey;
use App\Rules\General\Restriction\AgeGreaterThan;
use App\Rules\General\Restriction\AgeLessThan;
use App\Rules\General\Restriction\AllowedCity;
use App\Rules\General\Restriction\AllowedProvince;
use App\Rules\General\StringPattern\EnglishStringUsernameFormat;
use App\Rules\General\StringPattern\MinOneLowercase;
use App\Rules\General\StringPattern\MinOneNumber;
use App\Rules\General\StringPattern\MinOneUppercase;
use App\Rules\General\StringPattern\MobileNumberRule;
use App\Rules\General\StringPattern\PersianString;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterAttemptRequest extends FormRequest implements TabPageRequestInterface
{
    use AddAttributesPad;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->method() == HttpMethodEnum::POST->name;
    }

    /**
     * Get availabel tabs list
     *
     * @return array
     */
    public function tabsList(): array
    {
        return [
            RegisterBetconstructController::STEP_GET_MOBILE_NUMBER,
            RegisterBetconstructController::STEP_VERIFY_MOBILE_NUMBER,
            RegisterBetconstructController::STEP_GET_EMAIL,
            RegisterBetconstructController::STEP_VERIFY_EMAIL,
            RegisterBetconstructController::STEP_ACCOUNT_DATA,
            RegisterBetconstructController::STEP_FURTHER_INFORMATION,
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $tabpanel = $this->_tabpanel;

        $registrationFields = $this->getRegistrationFields();

        if ($tabpanel == RegisterBetconstructController::STEP_GET_MOBILE_NUMBER) {

            return [
                ClientModelEnum::MobilePhone->dbName()  => ['required', 'numeric', 'digits_between:8,15', new MobileNumberRule],
            ];
        } else if ($tabpanel == RegisterBetconstructController::STEP_VERIFY_MOBILE_NUMBER) {

            return [
                "MobileVerificationCode"  => ['required', 'numeric'],
            ];
        } else if ($tabpanel == RegisterBetconstructController::STEP_GET_EMAIL) {

            return [
                ClientModelEnum::Email->dbName()  => [
                    'required',
                    'email:rfc,strict,dns',
                    "min:" . config('hhh_config.validation.minLength.email'),
                    "max:" . config('hhh_config.validation.maxLength.email'),
                ],
            ];
        } else if ($tabpanel == RegisterBetconstructController::STEP_VERIFY_EMAIL) {

            return [
                "EmailVerificationCode"  => ['required', 'numeric'],
            ];
        } else if ($tabpanel == RegisterBetconstructController::STEP_ACCOUNT_DATA) {

            $sessionLocale = LocaleEnum::getSessionLocale();
            $localLettersRule = null;
            if ($sessionLocale == LocaleEnum::Persian)
                $localLettersRule = new PersianString;

            return [
                ClientModelEnum::Login->dbName()  => [
                    'bail',
                    'required',
                    new EnglishStringUsernameFormat,
                    "min:" . config('hhh_config.validation.minLength.usernameClient'),
                    "max:" . config('hhh_config.validation.maxLength.usernameClient'),
                    new UniqueSuperKey(User::class, $this[UsersTableEnum::Id->dbName()], [
                        UsersTableEnum::Username->dbName() => $this[ClientModelEnum::Login->dbName()],
                        UsersTableEnum::Type->dbName() => UsersTypesEnum::BetconstructClient->name,
                    ]),
                ],

                "regPassword"  => [
                    'required',
                    'min:' . config('hhh_config.validation.minLength.password'),
                    'max:' . config('hhh_config.validation.maxLength.password'),
                    new MinOneLowercase,
                    new MinOneUppercase,
                    new MinOneNumber,
                ],

                ClientModelEnum::FirstName->dbName() => ['bail', 'required', $localLettersRule],
                ClientModelEnum::LastName->dbName() => ['bail', 'required', $localLettersRule],
                ClientModelEnum::CurrencyId->dbName() => ['bail', 'required', Rule::in(json_decode(AppSettingsEnum::CommunityRegistrationAvailableCurrencies->getValue()))],
            ];
        } else if ($tabpanel == RegisterBetconstructController::STEP_FURTHER_INFORMATION && !empty($registrationFields)) {

            $provinceInternalCol = ClientModelEnum::ProvinceInternal->dbName();

            $birthDateDayKey = ClientModelEnum::BirthDateStamp->dbName() . '_day';
            $birthDateMonthKey = ClientModelEnum::BirthDateStamp->dbName() . '_month';
            $birthDateYearKey = ClientModelEnum::BirthDateStamp->dbName() . '_year';

            $rules = [

                ClientModelEnum::Gender->dbName() => Rule::in([ExternalAdminGendersEnum::Male->value, ExternalAdminGendersEnum::Female->value]),

                $birthDateDayKey => ['required', 'numeric', 'min:1', 'max:31'],
                $birthDateMonthKey => ['required', 'numeric', 'min:1', 'max:12'],
                $birthDateYearKey => ['required', 'numeric', 'digits:4'],
                ClientModelEnum::BirthDateStamp->dbName() => ['required', new AgeGreaterThan(18), new AgeLessThan(120)],

                ClientModelEnum::IBAN->dbName() => ['nullable', 'numeric', 'digits:24'],

                $provinceInternalCol => ['required', new AllowedProvince],
                ClientModelEnum::CityInternal->dbName() => ['required',  new AllowedCity($this->$provinceInternalCol)],
                ClientModelEnum::ContactNumbersInternal->dbName() => ['array', 'between:1,5'],
                ClientModelEnum::ContactNumbersInternal->dbName() . '.*' => ['numeric', 'digits_between:8,11'], // validate each item
                ClientModelEnum::ContactMethodsInternal->dbName() => ['array', 'min:1'],
                ClientModelEnum::CallerGenderInternal->dbName() => ['array', 'min:1'],
            ];

            $requiredRules = [];

            foreach ($rules as $key => $value) {

                if (in_array($key, $registrationFields)) {

                    if ($key == $birthDateDayKey || $key == $birthDateMonthKey || $key == $birthDateYearKey) {

                        if (in_array(ClientModelEnum::BirthDateStamp->dbName(), $registrationFields))
                            $requiredRules[$key] = $value;
                    } else {

                        $requiredRules[$key] = $value;
                    }
                }
            }

            return $requiredRules;
        } else {
            return [
                '_tabpanel' => ['required', Rule::in($this->tabsList())],
            ];
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [
            // Empty fields is for security reasons
            '_tabpanel' => "",
        ];

        $formAttributes = __('PagesContent_RegisaterationBetconstruct.form');

        foreach ($formAttributes as $attr => $details) {

            $attributes[$attr] = $details['name'];
        }

        // BirthDate attributes
        $birthDateStampCol = ClientModelEnum::BirthDateStamp->dbName();
        $attributes[$birthDateStampCol . '_day']    = __('general.timeInput.Day', ['attribute' => $attributes[$birthDateStampCol]]);
        $attributes[$birthDateStampCol . '_month']  = __('general.timeInput.Month', ['attribute' => $attributes[$birthDateStampCol]]);
        $attributes[$birthDateStampCol . '_year']   = __('general.timeInput.Year', ['attribute' => $attributes[$birthDateStampCol]]);

        $contactNumbersInternalCol = ClientModelEnum::ContactNumbersInternal->dbName();
        $attributes[$contactNumbersInternalCol . '.*'] = __('PagesContent_UserBetconstructProfile.form.' . $contactNumbersInternalCol . '.singleName');

        return $this->addPadToArrayVal($attributes);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->updateSessionRawData();

        $tabpanel = $this->_tabpanel;

        if ($tabpanel == RegisterBetconstructController::STEP_FURTHER_INFORMATION) {

            $registrationFields = $this->getRegistrationFields();

            $contactNumbersCol = ClientModelEnum::ContactNumbersInternal->dbName();
            $contactNumbers = $this->$contactNumbersCol;
            $contactNumbers = empty($contactNumbers) ? [] : array_values($contactNumbers);

            $contactMethodsInternalCol = ClientModelEnum::ContactMethodsInternal->dbName();
            $contactMethodsInternal = is_null($this->$contactMethodsInternalCol) ? [] : $this->$contactMethodsInternalCol;

            $callerGenderInternalCol = ClientModelEnum::CallerGenderInternal->dbName();
            $callerGenderInternal = is_null($this->$callerGenderInternalCol) ? [] : $this->$callerGenderInternalCol;

            $birthDateStampCol = ClientModelEnum::BirthDateStamp->dbName();

            if (in_array($birthDateStampCol, $registrationFields))
                $birthdate = $this->modifyDate($birthDateStampCol);
            else
                $birthdate = Carbon::now()->subYears(19)->toDateString(); // Default birthdate: 19 years old

            $this->merge([
                $birthDateStampCol => $birthdate,
                $contactNumbersCol => $contactNumbers,
                $contactMethodsInternalCol => $contactMethodsInternal,
                $callerGenderInternalCol => $callerGenderInternal,
            ]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $tabpanel = $this->_tabpanel;

        if ($tabpanel == RegisterBetconstructController::STEP_FURTHER_INFORMATION) {

            $contactNumbersCol = ClientModelEnum::ContactNumbersInternal->dbName();
            $contactNumbers = ArrayHelper::removeEmptyItems($this->$contactNumbersCol);
            $contactNumbers = empty($contactNumbers) ? [] : array_values($contactNumbers);

            $this->merge([
                $contactNumbersCol => $contactNumbers,
            ]);
        }
    }

    /**
     * Get registration fields
     *
     * @return array
     */
    private function getRegistrationFields(): array
    {
        $registrationFields = AppSettingsEnum::CommunityRegistrationFields->getValue();
        return is_null($registrationFields) ? [] : json_decode($registrationFields);
    }

    /**
     * Modify input date attribute
     *
     * @param  string $attrName
     * @return ?string
     */
    private function modifyDate(string $attrName): ?string
    {
        try {

            $calendarHelper = new CalendarHelper(
                AppSettingsEnum::CommunityTimeZone->getValue(),
                CalendarTypeEnum::getCase(AppSettingsEnum::CommunityCalendarType->getValue())
            );

            $dateYear = $this[$attrName . '_year'];
            $dateMonth = $this[$attrName . '_month'];
            $dateDay = $this[$attrName . '_day'];

            $separator = match ($calendarHelper->getCalendarType()) {
                CalendarTypeEnum::Gregorian => "-",
                CalendarTypeEnum::Persian => "/",

                default => "-"
            };

            $date = sprintf(
                "%s%s%s%s%s 12:00:00", // 12 o'clock is set to avoid time zone effects
                $dateYear,
                $separator,
                $dateMonth,
                $separator,
                $dateDay,
            );

            return sprintf("%s 12:00:00", Carbon::parse($calendarHelper->convertToUTC($date))->toDateString());
        } catch (\Throwable $th) {
            //throw $th;
        }

        return null;
    }

    /**
     * Update session raw data
     *
     * @return void
     */
    private function updateSessionRawData(): void
    {
        $data = $this->input();

        if (!empty($data)) {

            $rawData = GeneralSessionsEnum::SiteRegistrationRawData->getSession([]);
            $rawData = array_merge($rawData, $data);
            GeneralSessionsEnum::SiteRegistrationRawData->setSession($rawData);
        }
    }
}
