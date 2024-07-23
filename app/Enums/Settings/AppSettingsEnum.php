<?php

namespace App\Enums\Settings;

use App\Enums\General\CurrencyEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Users\ClientRegistrationAvailabelFieldsEnum;
use App\Enums\Users\PasswordRecoveryMethodEnum;
use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\CalendarTypeEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\Enums\PregPatternValidationEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Castable;
use App\Models\BackOffice\Settings\Setting;

enum AppSettingsEnum implements Translatable, Castable
{
    use EnumActions;

        // AdminPanel
    case IsAdminPanelActive;
    case AdminPanelExplanationInactive;
    case AdminPanelTimeZone;
    case canPersonnelChangeTimeZone;
    case AdminPanelCalendarType;
    case canPersonnelChangeCalendarType;
    case AdminPanelDefaultLanguage;
    case AdminPanelBigLogo;
    case AdminPanelMiniLogo;
    case AdminPanelFavicon;

        // Community
    case IsCommunityActive;
    case CommunityExplanationInactive;
    case CommunityTimeZone;
    case canClientChangeTimeZone;
    case CommunityCalendarType;
    case canClientChangeCalendarType;
    case CommunityDefaultLanguage;
    case CommentApproval;
    case SupportEmail;
    case CommunityBigLogo;
    case CommunityMiniLogo;
    case CommunityFavicon;
    case IsCommunityDashboradNoteActive;
    case CommunityDashboradNoteTitle;
    case CommunityDashboradNoteText;

        // Community Registration
    case CommunityRegistrationIsActive;
    case CommunityRegistrationFields;
    case CommunityRegistrationAvailableCurrencies;
    case CommunityRegistrationDefaultCurrency;
    case CommunityRegistrationTargetLinkAfterComplete;
    case CommunityRegistrationMobileVerificationIsRequired;
    case CommunityRegistrationMobileVerificationPerDay;
    case CommunityRegistrationMobileVerificationExpirationMinutes;
    case CommunityRegistrationMobileVerificationExpirationMinutesCoefficient;
    case CommunityRegistrationMobileVerificationText;
    case CommunityRegistrationEmailVerificationIsRequired;
    case CommunityRegistrationEmailVerificationPerDay;
    case CommunityRegistrationEmailVerificationExpirationMinutes;
    case CommunityRegistrationEmailVerificationExpirationMinutesCoefficient;
    case CommunityRegistrationEmailVerificationText;

        // Community Password Recovery
    case CommunityPasswordRecoveryIsActive;
    case CommunityPasswordRecoveryMethods;
    case CommunityPasswordRecoveryDefaultMethod;
    case CommunityPasswordRecoveryMobileVerificationPerDay;
    case CommunityPasswordRecoveryMobileVerificationExpirationMinutes;
    case CommunityPasswordRecoveryMobileVerificationText;
    case CommunityPasswordRecoveryEmailVerificationPerDay;
    case CommunityPasswordRecoveryEmailVerificationExpirationMinutes;

        // Chatbot
    case ChatbotProfileImage;
    case ChatbotInactiveChatsExpirationHours;
    case ChatbotClosedChatsDaysOfKeeping;

        // Ticket
    case TicketWaitingClientTicketsExpirationHours;
    case TicketClosedTicketsDaysOfKeeping;

        // Referral
    case ReferralIsActive;
    case ReferralIsActiveForTestClients;
    case ReferralAutoRenewLastSession;
    case ReferralPageNote;

        // Bet
    case BetDaysOfKeepingHistory;

        // TermsAndConditions
    case TermsAndConditions;

    /**
     * DomainGenerator
     * This section is managed by the "DomainGeneratorController" and it is not visible on the settings page.
     */
    case DomainGeneratorDomainCount;
    case DomainGeneratorDomainLettersCount;
    case DomainGeneratorExcludeLetters;
    case DomainGeneratorDomainExtension;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {

        return __('PagesContent_GeneralSettings.form.' . $this->name . '.name');
    }

    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        if (in_array('nullable', $this->validationRules()) && empty($value)) {
            return $value;
        }

        $castType = $this->castType();

        return $castType->cast($value);
    }

    /**
     * Get cast type of case
     *
     * @param  mixed $value
     * @return \App\HHH_Library\general\php\Enums\CastEnum
     */
    public function castType(): CastEnum
    {

        return match ($this) {

            // AdminPanel
            self::IsAdminPanelActive                => CastEnum::Boolean,
            self::AdminPanelExplanationInactive     => CastEnum::String,
            self::AdminPanelTimeZone                => CastEnum::String,
            self::canPersonnelChangeTimeZone        => CastEnum::Boolean,
            self::AdminPanelCalendarType            => CastEnum::String,
            self::canPersonnelChangeCalendarType    => CastEnum::Boolean,
            self::AdminPanelDefaultLanguage         => CastEnum::String,
            self::AdminPanelBigLogo                 => CastEnum::String,
            self::AdminPanelMiniLogo                => CastEnum::String,
            self::AdminPanelFavicon                 => CastEnum::String,

            // Community
            self::IsCommunityActive                 => CastEnum::Boolean,
            self::CommunityExplanationInactive      => CastEnum::String,
            self::CommunityTimeZone                 => CastEnum::String,
            self::canClientChangeTimeZone           => CastEnum::Boolean,
            self::CommunityCalendarType             => CastEnum::String,
            self::canClientChangeCalendarType       => CastEnum::Boolean,
            self::CommunityDefaultLanguage          => CastEnum::String,
            self::SupportEmail                      => CastEnum::String,
            self::CommentApproval                   => CastEnum::Boolean,
            self::CommunityBigLogo                  => CastEnum::String,
            self::CommunityMiniLogo                 => CastEnum::String,
            self::CommunityFavicon                  => CastEnum::String,
            self::IsCommunityDashboradNoteActive    => CastEnum::Boolean,
            self::CommunityDashboradNoteTitle       => CastEnum::String,
            self::CommunityDashboradNoteText        => CastEnum::String,

            // Community Registration
            self::CommunityRegistrationIsActive                                         => CastEnum::Boolean,
            self::CommunityRegistrationFields                                           => CastEnum::String, // JSON array list
            self::CommunityRegistrationAvailableCurrencies                              => CastEnum::String, // JSON array list
            self::CommunityRegistrationDefaultCurrency                                  => CastEnum::String,
            self::CommunityRegistrationTargetLinkAfterComplete                          => CastEnum::String,
            self::CommunityRegistrationMobileVerificationIsRequired                     => CastEnum::Boolean,
            self::CommunityRegistrationMobileVerificationPerDay                         => CastEnum::Int,
            self::CommunityRegistrationMobileVerificationExpirationMinutes              => CastEnum::Int,
            self::CommunityRegistrationMobileVerificationExpirationMinutesCoefficient   => CastEnum::Int,
            self::CommunityRegistrationMobileVerificationText                           => CastEnum::String,
            self::CommunityRegistrationEmailVerificationIsRequired                      => CastEnum::Boolean,
            self::CommunityRegistrationEmailVerificationPerDay                          => CastEnum::Int,
            self::CommunityRegistrationEmailVerificationExpirationMinutes               => CastEnum::Int,
            self::CommunityRegistrationEmailVerificationExpirationMinutesCoefficient    => CastEnum::Int,
            self::CommunityRegistrationEmailVerificationText                            => CastEnum::String,

            // Community Password Recovery
            self::CommunityPasswordRecoveryIsActive                             => CastEnum::Boolean,
            self::CommunityPasswordRecoveryMethods                              => CastEnum::String, // JSON array list
            self::CommunityPasswordRecoveryDefaultMethod                        => CastEnum::String,
            self::CommunityPasswordRecoveryMobileVerificationPerDay             => CastEnum::Int,
            self::CommunityPasswordRecoveryMobileVerificationExpirationMinutes  => CastEnum::Int,
            self::CommunityPasswordRecoveryMobileVerificationText               => CastEnum::String,
            self::CommunityPasswordRecoveryEmailVerificationPerDay              => CastEnum::Int,
            self::CommunityPasswordRecoveryEmailVerificationExpirationMinutes   => CastEnum::Int,

            // Chatbot
            self::ChatbotInactiveChatsExpirationHours   => CastEnum::Int,
            self::ChatbotClosedChatsDaysOfKeeping       => CastEnum::Int,

            // Ticket
            self::TicketWaitingClientTicketsExpirationHours => CastEnum::Int,
            self::TicketClosedTicketsDaysOfKeeping          => CastEnum::Int,

            // Referral
            self::ReferralIsActive                      => CastEnum::Boolean,
            self::ReferralIsActiveForTestClients        => CastEnum::Boolean,
            self::ReferralAutoRenewLastSession          => CastEnum::Boolean,
            self::ReferralPageNote                      => CastEnum::String,

            // Bet
            self::BetDaysOfKeepingHistory               => CastEnum::Int,

            // TermsAndConditions
            self::TermsAndConditions                => CastEnum::String,

            // DomainGenerator
            self::DomainGeneratorDomainCount        => CastEnum::Int,
            self::DomainGeneratorDomainLettersCount => CastEnum::Int,
            self::DomainGeneratorExcludeLetters     => CastEnum::String,
            self::DomainGeneratorDomainExtension    => CastEnum::Int,

            default => CastEnum::String
        };
    }

    /**
     * Get the validation rules for case
     *
     * @param bool $forSettingRequest
     * @return array
     */
    public function validationRules(bool $forSettingRequest = false): array
    {

        $imageRules = $this->imageRules($forSettingRequest);

        return match ($this) {

            // AdminPanel
            self::IsAdminPanelActive                => ['required', 'boolean'],
            self::AdminPanelExplanationInactive     => ['nullable'],
            self::AdminPanelTimeZone                => ['required', PregPatternValidationEnum::Timezone->regex()],
            self::canPersonnelChangeTimeZone        => ['required', 'boolean'],
            self::AdminPanelCalendarType            => ['required'],
            self::canPersonnelChangeCalendarType    => ['required', 'boolean'],
            self::AdminPanelDefaultLanguage         => ['required'],
            self::AdminPanelBigLogo                 => $imageRules[self::AdminPanelBigLogo->name],
            self::AdminPanelMiniLogo                => $imageRules[self::AdminPanelMiniLogo->name],
            self::AdminPanelFavicon                 => $imageRules[self::AdminPanelFavicon->name],

            // Community
            self::IsCommunityActive                 => ['required', 'boolean'],
            self::CommunityExplanationInactive      => ['nullable'],
            self::CommunityTimeZone                 => ['required', PregPatternValidationEnum::Timezone->regex()],
            self::canClientChangeTimeZone           => ['required', 'boolean'],
            self::CommunityCalendarType             => ['required'],
            self::canClientChangeCalendarType       => ['required', 'boolean'],
            self::CommunityDefaultLanguage          => ['required'],
            self::CommentApproval                   => ['required', 'boolean'],
            self::SupportEmail                      => ['nullable', 'email:rfc,strict'],
            self::CommunityBigLogo                  => $imageRules[self::CommunityBigLogo->name],
            self::CommunityMiniLogo                 => $imageRules[self::CommunityMiniLogo->name],
            self::CommunityFavicon                  => $imageRules[self::CommunityFavicon->name],
            self::IsCommunityDashboradNoteActive    => ['required', 'boolean'],

            // Community Registration
            self::CommunityRegistrationIsActive                                         => ['required', 'boolean'],
            self::CommunityRegistrationFields                                           => ['nullable'],
            self::CommunityRegistrationAvailableCurrencies                              => ['required', 'min:1'],
            self::CommunityRegistrationDefaultCurrency                                  => ['required'],
            self::CommunityRegistrationTargetLinkAfterComplete                          => ['nullable', PregPatternValidationEnum::Url->regex()],
            self::CommunityRegistrationMobileVerificationIsRequired                     => ['required', 'boolean'],
            self::CommunityRegistrationMobileVerificationPerDay                         => ['required', 'numeric', 'min:1'],
            self::CommunityRegistrationMobileVerificationExpirationMinutes              => ['required', 'numeric', 'min:1', 'max:15'],
            self::CommunityRegistrationMobileVerificationExpirationMinutesCoefficient   => ['required', 'numeric', 'min:0'],
            self::CommunityRegistrationMobileVerificationText                           => ['required'],
            self::CommunityRegistrationEmailVerificationIsRequired                      => ['required', 'boolean'],
            self::CommunityRegistrationEmailVerificationPerDay                          => ['required', 'numeric', 'min:1'],
            self::CommunityRegistrationEmailVerificationExpirationMinutes               => ['required', 'numeric', 'min:3', 'max:20'],
            self::CommunityRegistrationEmailVerificationExpirationMinutesCoefficient    => ['required', 'numeric', 'min:0'],
            self::CommunityRegistrationEmailVerificationText                            => ['required'],

            // Community Password Recovery
            self::CommunityPasswordRecoveryIsActive                             => ['required', 'boolean'],
            self::CommunityPasswordRecoveryMethods                              => ['required', 'min:1'],
            self::CommunityPasswordRecoveryDefaultMethod                        => ['required'],
            self::CommunityPasswordRecoveryMobileVerificationPerDay             => ['required', 'numeric', 'min:1'],
            self::CommunityPasswordRecoveryMobileVerificationExpirationMinutes  => ['required', 'numeric', 'min:1', 'max:15'],
            self::CommunityPasswordRecoveryMobileVerificationText               => ['required'],
            self::CommunityPasswordRecoveryEmailVerificationPerDay              => ['required', 'numeric', 'min:1'],
            self::CommunityPasswordRecoveryEmailVerificationExpirationMinutes   => ['required', 'numeric', 'min:3', 'max:20'],

            // Chatbot
            self::ChatbotProfileImage                   => $imageRules[self::ChatbotProfileImage->name],
            self::ChatbotInactiveChatsExpirationHours   => ['required', 'numeric', 'min:1', 'max:72'],
            self::ChatbotClosedChatsDaysOfKeeping       => ['required', 'numeric', 'min:1', 'max:60'],

            // Ticket
            self::TicketWaitingClientTicketsExpirationHours => ['required', 'numeric', 'min:1', 'max:72'],
            self::TicketClosedTicketsDaysOfKeeping          => ['required', 'numeric', 'min:1', 'max:365'],

            // Referral
            self::ReferralIsActive                      => ['required', 'boolean'],
            self::ReferralIsActiveForTestClients        => ['required', 'boolean'],
            self::ReferralAutoRenewLastSession          => ['required', 'boolean'],
            self::ReferralPageNote                      => ['nullable'],

            // Bet
            self::BetDaysOfKeepingHistory               => ['required', 'numeric', 'min:30', 'max:100'],

            // TermsAndConditions
            self::TermsAndConditions => ['nullable'],

            default => []
        };
    }

    /**
     * Get the default value of case
     *
     * @param  bool $cast
     * @return mixed
     */
    public function defaultValue(bool $cast = true): mixed
    {
        $value = match ($this) {

            // AdminPanel
            self::IsAdminPanelActive                => '1',
            self::AdminPanelExplanationInactive     => 'The system is under maintenance and we will be back soon.',
            self::AdminPanelTimeZone                => '+03:30',
            self::canPersonnelChangeTimeZone        => '1',
            self::AdminPanelCalendarType            => CalendarTypeEnum::Gregorian->name,
            self::canPersonnelChangeCalendarType    => '1',
            self::AdminPanelDefaultLanguage         => LocaleEnum::Persian->name,
            self::AdminPanelBigLogo                 => null,
            self::AdminPanelMiniLogo                => null,
            self::AdminPanelFavicon                 => null,

            // Community
            self::IsCommunityActive                 => '1',
            self::CommunityExplanationInactive      => 'The system is under maintenance and we will be back soon.',
            self::CommunityTimeZone                 => '+03:30',
            self::canClientChangeTimeZone           => '1',
            self::CommunityCalendarType             => CalendarTypeEnum::Persian->name,
            self::canClientChangeCalendarType       => '1',
            self::CommunityDefaultLanguage          => LocaleEnum::Persian->name,
            self::CommentApproval                   => '1',
            self::SupportEmail                      => config('app.support_email'),
            self::CommunityBigLogo                  => null,
            self::CommunityMiniLogo                 => null,
            self::CommunityFavicon                  => null,
            self::IsCommunityDashboradNoteActive    => '0',
            self::CommunityDashboradNoteTitle       => null,
            self::CommunityDashboradNoteText        => null,

            // Community Registration
            self::CommunityRegistrationIsActive                                         => '0',
            self::CommunityRegistrationFields                                           => json_encode(ClientRegistrationAvailabelFieldsEnum::names()),
            self::CommunityRegistrationAvailableCurrencies                              => json_encode(CurrencyEnum::names()),
            self::CommunityRegistrationDefaultCurrency                                  => CurrencyEnum::IRT->name,
            self::CommunityRegistrationTargetLinkAfterComplete                          => SitePublicRoutesEnum::Profile->url(),
            self::CommunityRegistrationMobileVerificationIsRequired                     => '0',
            self::CommunityRegistrationMobileVerificationPerDay                         => '24',
            self::CommunityRegistrationMobileVerificationExpirationMinutes              => '1',
            self::CommunityRegistrationMobileVerificationExpirationMinutesCoefficient   => '3',
            self::CommunityRegistrationMobileVerificationText                           => 'Your verification code is: {verificationCode}',
            self::CommunityRegistrationEmailVerificationIsRequired                      => '0',
            self::CommunityRegistrationEmailVerificationPerDay                          => '24',
            self::CommunityRegistrationEmailVerificationExpirationMinutes               => '4',
            self::CommunityRegistrationEmailVerificationExpirationMinutesCoefficient    => '2',
            self::CommunityRegistrationEmailVerificationText                            => 'Your verification code is: {verificationCode}',

            // Community Password Recovery
            self::CommunityPasswordRecoveryIsActive                             => '0',
            self::CommunityPasswordRecoveryMethods                              => json_encode(PasswordRecoveryMethodEnum::names()),
            self::CommunityPasswordRecoveryDefaultMethod                        => PasswordRecoveryMethodEnum::Email->name,
            self::CommunityPasswordRecoveryMobileVerificationPerDay             => '24',
            self::CommunityPasswordRecoveryMobileVerificationExpirationMinutes  => '1',
            self::CommunityPasswordRecoveryMobileVerificationText               => 'Your verification code is: {verificationCode}',
            self::CommunityPasswordRecoveryEmailVerificationPerDay              => '24',
            self::CommunityPasswordRecoveryEmailVerificationExpirationMinutes   => '4',

            // Chatbot
            self::ChatbotInactiveChatsExpirationHours   => 1,
            self::ChatbotClosedChatsDaysOfKeeping       => 30,

            // Ticket
            self::TicketWaitingClientTicketsExpirationHours => 12,
            self::TicketClosedTicketsDaysOfKeeping          => 365,

            // Referral
            self::ReferralIsActive                      => 0,
            self::ReferralIsActiveForTestClients        => 0,
            self::ReferralAutoRenewLastSession          => 0,
            self::ReferralPageNote                      => null,

            // Bet
            self::BetDaysOfKeepingHistory               => 30,

            // TermsAndConditions
            self::TermsAndConditions                => '',

            // DomainGenerator
            self::DomainGeneratorDomainCount        => 1,
            self::DomainGeneratorDomainLettersCount => 7,
            self::DomainGeneratorExcludeLetters     => "xx+porn+sex+bet",
            self::DomainGeneratorDomainExtension    => 0,

            default => ''
        };

        return $cast ? $this->cast($value) : $value;
    }

    /**
     * Register only image cases here
     *
     * @return array
     */
    public static function imageCases(): array
    {
        return [
            AppSettingsEnum::AdminPanelBigLogo,
            AppSettingsEnum::AdminPanelMiniLogo,
            AppSettingsEnum::AdminPanelFavicon,

            AppSettingsEnum::CommunityBigLogo,
            AppSettingsEnum::CommunityMiniLogo,
            AppSettingsEnum::CommunityFavicon,

            AppSettingsEnum::ChatbotProfileImage,
        ];
    }

    /**
     * Get image cases rules
     *
     * @param bool $forSettingRequest
     * @return array
     */
    private function imageRules(bool $forSettingRequest = false): array
    {

        $rules = [];
        foreach (self::imageCases() as $case) {

            if ($forSettingRequest) {

                // Uploading image
                $fileConfig = Setting::getItemFullRecord($case)->getPhotoFileConfig();

                $rules[$case->name] = [
                    'nullable',
                    // 'image', // This rule does not work for .ico (favicon)
                    "mimes:" . $fileConfig->mimes(),
                    sprintf("dimensions:min_width=%s,min_height=%s", $fileConfig->minWidth(), $fileConfig->minHeight()),
                    sprintf("dimensions:max_width=%s,max_height=%s", $fileConfig->maxWidth(), $fileConfig->maxHeight()),
                    "min:" . $fileConfig->minSize(),
                    "max:" . $fileConfig->maxSize(),

                ];
            } else {

                // Save value in settings table
                $rules[$case->name] = [
                    'nullable',
                ];
            }
        }

        return $rules;
    }

    /************************* Fetch data from setting **************************/

    /**
     * Get the stored value of the case in the database
     *
     * @param  mixed $default
     * @return mixed
     */
    public function getValue(mixed $default = null): mixed
    {
        return Setting::get($this, $default);
    }

    /**
     * Save the value of the case in the database
     *
     * @param  mixed $value
     * @return mixed
     */
    public function setValue(mixed $value = null): mixed
    {
        return Setting::set($this, $value);
    }

    /**
     * Get the url of the image case
     *
     * @param bool $useFallbackPhoto : true => If the file does not exist, the return image will be the one defined in the "settings" configuration
     * @return mixed
     */
    public function getImageUrl(bool $useFallbackPhoto = true): mixed
    {
        return Setting::getItemFullRecord($this)
            ->getPhotoFileAssistant($useFallbackPhoto)
            ->getUrl();
    }

    /**
     * Get the relative path of the image case
     *
     * @param bool $useFallbackPhoto : true => If the file does not exist, the return image will be the one defined in the "settings" configuration
     * @return mixed
     */
    public function getImageRelativePath(bool $useFallbackPhoto = true): mixed
    {
        return Setting::getItemFullRecord($this)
            ->getPhotoFileAssistant($useFallbackPhoto)
            ->getRelativePath();
    }
    /************************* Fetch data from setting END **************************/
}
