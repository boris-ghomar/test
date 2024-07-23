<?php

namespace App\Enums\Settings;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\Enums\PregPatternValidationEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ApiConfigEnum as ExternalAdminApiConfigEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ApiConfigEnum as SwarmApiConfigEnum;
use App\HHH_Library\ThisApp\API\JustCall\Enums\ApiConfigEnum as JusctCallApiConfigEnum;
use App\Interfaces\Castable;
use App\Models\BackOffice\Settings\TechnicalSetting;


enum AppTechnicalSettingsEnum implements Translatable, Castable
{
    use EnumActions;

        // Betconstruct External Admin API
    case BcExAd_ApiName;
    case BcExAd_HashAlgorithm;
    case BcExAd_ApiUrl;
    case BcExAd_PartnerId;
    case BcExAd_HashKey;

        // Betconstruct Swarm API
    case BcSwAp_ApiName;
    case BcSwAp_ApiUrl;
    case BcSwAp_WebSocketUrl;
    case BcSwAp_WebSocketUrlAlternative;
    case BcSwAp_SiteId;

        // Trust Score System
    case TrScSy_NewClientBaseTrustScore;
    case TrScSy_NegativePointValue;
    case TrScSy_DepositPerPoint;
    case TrScSy_UsdPerPoint;
    case TrScSy_IrtPerPoint;
    case TrScSy_TomPerPoint;
    case TrScSy_IrrPerPoint;

        // Domains Assignment System
    case DoAsSy_PermanentDomain; // Partner permanent domain
    case DoAsSy_MinReportCount; // Minimum report count to assign new domain
    case DoAsSy_MinAssignableTrustScore; // Minimum trust score for assign dedicated domain
    case DoAsSy_MaxAssignableDomains;
    case DoAsSy_MinPublicDomainReportsCount; // Minimum report count to assign new public domain
    case DoAsSy_MinPublicDomainHoldMinutes; // Minimum hold time (minutes) to assign new public domain
    case DoAsSy_DaysOfKeepingExipredAssignments;

        // JustCall VOIP Service Provider API
    case JuCaAp_ApiName;
    case JuCaAp_ApiUrl;
    case JuCaAp_ApiKey;
    case JuCaAp_ApiSecret;
    case JuCaAp_PhoneNumberForSMS;

    // consts
    const BC_EXTERNAL_ADMIN_PREFIX  = "BcExAd_"; // Used in ConfigEnum
    const BC_SWARM_API_PREFIX       = "BcSwAp_"; // Used in ConfigEnum
    const JustCall_API_PREFIX       = "JuCaAp_"; // Used in ConfigEnum

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {

        return __('PagesContent_TechnicalSettings.form.' . $this->name . '.name');
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
        // The default is String, so only register the non-string casts
        return match ($this) {

            //  Betconstruct External Admin API
            self::BcExAd_PartnerId  => CastEnum::Int,

            // Betconstruct Swarm API
            self::BcSwAp_SiteId     => CastEnum::Int,

            // Trust Score System
            self::TrScSy_NewClientBaseTrustScore    => CastEnum::Int,
            self::TrScSy_NegativePointValue         => CastEnum::Int,
            self::TrScSy_DepositPerPoint            => CastEnum::Int,
            self::TrScSy_UsdPerPoint                => CastEnum::Int,
            self::TrScSy_IrtPerPoint                => CastEnum::Int,
            self::TrScSy_TomPerPoint                => CastEnum::Int,
            self::TrScSy_IrrPerPoint                => CastEnum::Int,

            // Domains Assignment System
            self::DoAsSy_MinReportCount                     => CastEnum::Int,
            self::DoAsSy_MinAssignableTrustScore            => CastEnum::Int,
            self::DoAsSy_MaxAssignableDomains               => CastEnum::Int,
            self::DoAsSy_MinPublicDomainReportsCount        => CastEnum::Int,
            self::DoAsSy_MinPublicDomainHoldMinutes         => CastEnum::Int,
            self::DoAsSy_DaysOfKeepingExipredAssignments    => CastEnum::Int,

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

            // Betconstruct External Admin API
            self::BcExAd_ApiName        => ['required', PregPatternValidationEnum::EnglishString->regex()],
            self::BcExAd_HashAlgorithm  => ['required', PregPatternValidationEnum::EnglishString->regex()],
            self::BcExAd_ApiUrl         => ['required', PregPatternValidationEnum::Url->regex()],
            self::BcExAd_PartnerId      => ['required', 'numeric'],
            self::BcExAd_HashKey        => ['required', PregPatternValidationEnum::EnglishString->regex()],

            // Betconstruct Swarm API
            self::BcSwAp_ApiName                    => ['required', PregPatternValidationEnum::EnglishString->regex()],
            self::BcSwAp_ApiUrl                     => ['required', PregPatternValidationEnum::Url->regex()],
            self::BcSwAp_WebSocketUrl               => ['required', PregPatternValidationEnum::Websocket->regex()],
            self::BcSwAp_WebSocketUrlAlternative    => ['required', PregPatternValidationEnum::Websocket->regex()],
            self::BcSwAp_SiteId                     => ['required', 'numeric'],

            // Trust Score System
            self::TrScSy_NewClientBaseTrustScore    => ['required', 'numeric', 'min:1', 'max:100'],
            self::TrScSy_NegativePointValue         => ['required', 'numeric', 'min:1'],
            self::TrScSy_DepositPerPoint            => ['required', 'numeric', 'min:1'],
            self::TrScSy_UsdPerPoint                => ['required', 'numeric', 'min:1'],
            self::TrScSy_IrtPerPoint                => ['required', 'numeric', 'min:1'],
            self::TrScSy_TomPerPoint                => ['required', 'numeric', 'min:1'],
            self::TrScSy_IrrPerPoint                => ['required', 'numeric', 'min:1'],

            // Domains Assignment System
            self::DoAsSy_PermanentDomain                    => ['required'],
            self::DoAsSy_MinReportCount                     => ['required', 'numeric', 'min:1'],
            self::DoAsSy_MinAssignableTrustScore            => ['required', 'numeric', 'min:1', 'max:100'],
            self::DoAsSy_MaxAssignableDomains               => ['required', 'numeric', 'min:1', 'max:100'],
            self::DoAsSy_MinPublicDomainReportsCount        => ['required', 'numeric', 'min:1'],
            self::DoAsSy_MinPublicDomainHoldMinutes         => ['nullable', 'numeric', 'min:1'],
            self::DoAsSy_DaysOfKeepingExipredAssignments    => ['required', 'numeric', 'min:10', 'max:365'],

            // JustCall API
            self::JuCaAp_ApiName            => ['required', PregPatternValidationEnum::EnglishString->regex()],
            self::JuCaAp_ApiUrl             => ['required', PregPatternValidationEnum::Url->regex()],
            self::JuCaAp_ApiKey             => ['required'],
            self::JuCaAp_ApiSecret          => ['required'],
            self::JuCaAp_PhoneNumberForSMS  => ['required', PregPatternValidationEnum::MobileNumber->regex()],

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

            // Betconstruct External Admin API
            self::BcExAd_ApiName        => ExternalAdminApiConfigEnum::ApiName->value,
            self::BcExAd_HashAlgorithm  => ExternalAdminApiConfigEnum::HashAlgorithm->value,
            self::BcExAd_ApiUrl         => ExternalAdminApiConfigEnum::ApiUrl->value,
            self::BcExAd_PartnerId      => ExternalAdminApiConfigEnum::PartnerId->value,
            self::BcExAd_HashKey        => ExternalAdminApiConfigEnum::HashKey->value,

            // Betconstruct Swarm API
            self::BcSwAp_ApiName                    => SwarmApiConfigEnum::ApiName->value,
            self::BcSwAp_ApiUrl                     => SwarmApiConfigEnum::ApiUrl->value,
            self::BcSwAp_WebSocketUrl               => SwarmApiConfigEnum::WebSocketUrl->value,
            self::BcSwAp_WebSocketUrlAlternative    => SwarmApiConfigEnum::WebSocketUrlAlternative->value,
            self::BcSwAp_SiteId                     => SwarmApiConfigEnum::SiteId->value,

            // Trust Score System
            self::TrScSy_NewClientBaseTrustScore    => 5,
            self::TrScSy_NegativePointValue         => 1,
            self::TrScSy_DepositPerPoint            => 10,
            self::TrScSy_UsdPerPoint                => 20,
            self::TrScSy_IrtPerPoint                => 1000,
            self::TrScSy_TomPerPoint                => 1000000,
            self::TrScSy_IrrPerPoint                => 10000000,

            // Domains Assignment System
            self::DoAsSy_PermanentDomain                    => 'betcart.com',
            self::DoAsSy_MinReportCount                     => 20,
            self::DoAsSy_MinAssignableTrustScore            => 91,
            self::DoAsSy_MaxAssignableDomains               => 1,
            self::DoAsSy_MinPublicDomainReportsCount        => 15,
            self::DoAsSy_MinPublicDomainHoldMinutes         => 30,
            self::DoAsSy_DaysOfKeepingExipredAssignments    => 90,

            // JustCall API
            self::JuCaAp_ApiName            => JusctCallApiConfigEnum::ApiName->value,
            self::JuCaAp_ApiUrl             => JusctCallApiConfigEnum::ApiUrl->value,
            self::JuCaAp_ApiKey             => "",
            self::JuCaAp_ApiSecret          => "",
            self::JuCaAp_PhoneNumberForSMS  => "",

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
            //
        ];
    }

    /**
     * Register only cases, that need to save in database with encryption
     *
     * @return array
     */
    public static function cryptCases(): array
    {
        return [
            self::BcExAd_HashKey,
            self::BcExAd_PartnerId,

            self::BcSwAp_SiteId,

            self::JuCaAp_ApiKey,
            self::JuCaAp_ApiSecret,
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
                $fileConfig = TechnicalSetting::getItemFullRecord($case)->getPhotoFileConfig();

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
        return TechnicalSetting::get($this, $default);
    }

    /**
     * Save the value of the case in the database
     *
     * @param  mixed $value
     * @return mixed
     */
    public function setValue(mixed $value = null): mixed
    {
        return TechnicalSetting::set($this, $value);
    }

    /**
     * Get the url of the image case
     *
     * @param bool $useFallbackPhoto : true => If the file does not exist, the return image will be the one defined in the "hhh_config" configuration
     * @return mixed
     */
    public function getImageUrl(bool $useFallbackPhoto = true): mixed
    {
        return TechnicalSetting::getItemFullRecord($this)
            ->getPhotoFileAssistant($useFallbackPhoto)
            ->getUrl();
    }
    /************************* Fetch data from setting END **************************/
}
