<?php

namespace App\Http\Requests\Site;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\ArrayHelper;
use App\HHH_Library\general\php\Enums\PregPatternValidationEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\GendersEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Interfaces\Request\TabPageRequestInterface;
use App\Models\User;
use App\Rules\General\Restriction\AgeGreaterThan;
use App\Rules\General\Restriction\AgeLessThan;
use App\Rules\General\Restriction\AllowedCity;
use App\Rules\General\Restriction\AllowedProvince;
use App\Rules\General\StringPattern\MinOneLowercase;
use App\Rules\General\StringPattern\MinOneNumber;
use App\Rules\General\StringPattern\MinOneUppercase;
use App\Rules\Site\Profile\AllowedJobField;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserBetconstructProfileRequest extends SuperRequest implements TabPageRequestInterface
{
    private const
        TAB_ACCOUNT = "Account",
        TAB_FURTHER_INFORMATION = "FurtherInformation",
        TAB_CHANGE_EMAIL = "ChangeEmail",
        TAB_PASSWORD = "Password",
        TAB_PHOTO = "Photo",
        TAB_SETTINGS = "Settings";

    private const
        API_ACITON_SEND_EMAIL_VERIFICATION = "send_email_verification";

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = User::authUser();
        return ($this->id === $user->id && $user->isClient());
    }

    /**
     * Get availabel tabs list
     *
     * @return array
     */
    public function tabsList(): array
    {
        return [
            self::TAB_ACCOUNT,
            self::TAB_FURTHER_INFORMATION,
            self::TAB_CHANGE_EMAIL,
            self::TAB_PASSWORD,
            self::TAB_PHOTO,
            self::TAB_SETTINGS,
        ];
    }

    /**
     * Get availabel api actions list
     *
     * @return array
     */
    private function apiAcionsList(): array
    {
        return [
            self::API_ACITON_SEND_EMAIL_VERIFICATION,
        ];
    }

    /**
     * Get email rules
     *
     * @return array
     */
    private function getEmailRules(bool $required): array
    {

        return [
            $required ? 'required' : 'nullable',
            'email:rfc,strict,dns',
            "min:" . config('hhh_config.validation.minLength.email'),
            "max:" . config('hhh_config.validation.maxLength.email'),
        ];
    }
    /******************** Action rules *********************/

    /**
     * Rules for store a newly created resource in storage.
     *
     * @return array
     */
    public function rulesStore(): array
    {
        // Used for api request with POST method

        $apiAction = $this->_apiAction;

        if ($apiAction == self::API_ACITON_SEND_EMAIL_VERIFICATION) {

            return [
                ClientModelEnum::Email->dbName() => $this->getEmailRules(true),
            ];
        } else {

            return [
                '_apiAction' => ['required', Rule::in($this->apiAcionsList())],
            ];
        }
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        $tabpanel = $this->_tabpanel;

        if ($tabpanel == self::TAB_ACCOUNT) {
            return [];
        } else if ($tabpanel == self::TAB_FURTHER_INFORMATION) {
            $provinceInternalCol = ClientModelEnum::ProvinceInternal->dbName();

            return [

                /**
                 * If need to change, also change "isFurtherInformationTabCompleted"
                 * in the "UserBetconstructProfileController" controller
                 */

                ClientModelEnum::Gender->dbName() => Rule::in([GendersEnum::Male->value, GendersEnum::Female->value]),

                ClientModelEnum::BirthDateStamp->dbName() . '_day' => ['required', 'numeric', 'min:1', 'max:31'],
                ClientModelEnum::BirthDateStamp->dbName() . '_month' => ['required', 'numeric', 'min:1', 'max:12'],
                ClientModelEnum::BirthDateStamp->dbName() . '_year' => ['required', 'numeric', 'digits:4'],
                ClientModelEnum::BirthDateStamp->dbName() => ['required', new AgeGreaterThan(18), new AgeLessThan(120)],

                ClientModelEnum::IBAN->dbName() => ['nullable', 'numeric', 'digits:24'],

                $provinceInternalCol => ['required', new AllowedProvince],
                ClientModelEnum::CityInternal->dbName() => ['required',  new AllowedCity($this->$provinceInternalCol)],
                ClientModelEnum::JobFieldInternal->dbName() => ['nullable',  new AllowedJobField],
                ClientModelEnum::ContactNumbersInternal->dbName() => ['array', 'between:1,5'],
                ClientModelEnum::ContactNumbersInternal->dbName() . '.*' => ['numeric', 'digits_between:8,11'], // validate each item
                ClientModelEnum::ContactMethodsInternal->dbName() => ['array', 'min:1'],
                ClientModelEnum::CallerGenderInternal->dbName() => ['array', 'min:1'],
            ];
        } else if ($tabpanel == self::TAB_CHANGE_EMAIL) {

            $emailCol = ClientModelEnum::Email->dbName();

            return [
                $emailCol => $this->getEmailRules(false),
                "emailVerificationCode" => ['nullable', 'numeric'],
            ];
        } else if ($tabpanel == self::TAB_PASSWORD) {

            return [
                /**
                 * password:web
                 * https://laravel.com/docs/10.x/validation#rule-password
                 *
                 * authentication guard, comes from: '\config\auth.php'
                 */
                'current_password' => [
                    'nullable',
                    'required_with:new_password',
                    'different:new_password',
                    'current_password:web',
                ],
                'new_password' => [
                    'nullable',
                    'min:' . config('hhh_config.validation.minLength.password'),
                    'max:' . config('hhh_config.validation.maxLength.password'),
                    new MinOneLowercase,
                    new MinOneUppercase,
                    new MinOneNumber,
                ],
                'new_password_confirmation' => [
                    'required_with:new_password',
                    'same:new_password'
                ],
            ];
        } else if ($tabpanel == self::TAB_PHOTO) {

            $profilePhoto = ImageConfigEnum::ProfilePhoto;

            return [

                UsersTableEnum::ProfilePhotoName->dbName() => [
                    'image',
                    "mimes:" . $profilePhoto->mimes(),
                    sprintf("dimensions:min_width=%s,min_height=%s", $profilePhoto->minWidth(), $profilePhoto->minHeight()),
                    sprintf("dimensions:max_width=%s,max_height=%s", $profilePhoto->maxWidth(), $profilePhoto->maxHeight()),
                    "min:" . $profilePhoto->minSize(),
                    "max:" . $profilePhoto->maxSize(),
                ],
            ];
        } else if ($tabpanel == self::TAB_SETTINGS) {

            $case = AppSettingsEnum::CommunityTimeZone;
            $rules[$case->name] = ['nullable', PregPatternValidationEnum::Timezone->regex()];

            $case = AppSettingsEnum::CommunityCalendarType;
            $rules[$case->name] = ['nullable'];

            return $rules;
        } else {
            return [
                '_tabpanel' => ['required', Rule::in($this->tabsList())],
            ];
        }
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {
        // disabled from controller
        return [];
    }

    /******************** Action rules END *********************/

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
            '_apiAction'   => "",
        ];

        $formAttributes = __('PagesContent_UserBetconstructProfile.form');

        foreach ($formAttributes as $attr => $details) {

            $attributes[$attr] = $details['name'];
        }

        // BirthDate attributes
        $birthDateStampCol = ClientModelEnum::BirthDateStamp->dbName();
        $attributes[$birthDateStampCol . '_day']    = __('general.timeInput.Day', ['attribute' => $attributes[$birthDateStampCol]]);
        $attributes[$birthDateStampCol . '_month']  = __('general.timeInput.Month', ['attribute' => $attributes[$birthDateStampCol]]);
        $attributes[$birthDateStampCol . '_year']   = __('general.timeInput.Year', ['attribute' => $attributes[$birthDateStampCol]]);

        $contactNumbersInternalCol = ClientModelEnum::ContactNumbersInternal->dbName();
        $attributes[$contactNumbersInternalCol . '.*'] = __('PagesContent_UserBetconstructProfile.form.' . $contactNumbersInternalCol . '.singleName');;

        return $this->addPadToArrayVal($attributes);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $tabpanel = $this->_tabpanel;

        $this->merge([
            UsersTableEnum::Id->dbName() => $this->user()->id,
        ]);

        if ($tabpanel == self::TAB_ACCOUNT) {
            //
        } else if ($tabpanel == self::TAB_FURTHER_INFORMATION) {

            $contactNumbersCol = ClientModelEnum::ContactNumbersInternal->dbName();
            $contactNumbers = $this->$contactNumbersCol;
            $contactNumbers = empty($contactNumbers) ? [] : array_values($contactNumbers);

            $contactMethodsInternalCol = ClientModelEnum::ContactMethodsInternal->dbName();
            $contactMethodsInternal = is_null($this->$contactMethodsInternalCol) ? [] : $this->$contactMethodsInternalCol;

            $callerGenderInternalCol = ClientModelEnum::CallerGenderInternal->dbName();
            $callerGenderInternal = is_null($this->$callerGenderInternalCol) ? [] : $this->$callerGenderInternalCol;

            $this->merge([
                ClientModelEnum::BirthDateStamp->dbName() => $this->modifyDate(ClientModelEnum::BirthDateStamp->dbName()),
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

        if ($tabpanel == self::TAB_FURTHER_INFORMATION) {

            $contactNumbersCol = ClientModelEnum::ContactNumbersInternal->dbName();
            $contactNumbers = ArrayHelper::removeEmptyItems($this->$contactNumbersCol);
            $contactNumbers = empty($contactNumbers) ? [] : array_values($contactNumbers);

            $this->merge([
                $contactNumbersCol => $contactNumbers,
            ]);
        }
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
            $user = User::authUser();

            $dateOldValue = $this->$attrName;

            $dateYear = $this[$attrName . '_year'];
            $dateMonth = $this[$attrName . '_month'];
            $dateDay = $this[$attrName . '_day'];

            if (Str::of($dateOldValue)->contains('/')) {
                $separator = '/';
            } elseif (Str::of($dateOldValue)->contains('-')) {
                $separator = '-';
            } else
                $separator = '-';

            $date = sprintf(
                "%s%s%s%s%s 12:00:00", // 12 o'clock is set to avoid time zone effects
                $dateYear,
                $separator,
                $dateMonth,
                $separator,
                $dateDay,
            );

            return sprintf("%s 12:00:00", Carbon::parse($user->convertLocalTimeToUTC($date))->toDateString());
        } catch (\Throwable $th) {
            //throw $th;
        }

        return null;
    }
}
