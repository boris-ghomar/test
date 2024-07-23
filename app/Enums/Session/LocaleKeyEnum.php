<?php

namespace App\Enums\Session;

use App\Enums\Routes\RouteTypesEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\Enums\EnumSession;
use App\Models\General\UserSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

enum LocaleKeyEnum: string
{
    use EnumActions;
    use EnumSession;


    case AdminPanel = "admin_panel_locale";
    case Community = "community_locale";

    /******************** static methods ********************/

    /**
     * Setup session for locale base on route URI
     *
     * @param bool $force : Force setup locale, even if the session exists
     * @return void
     */
    public static function setupSessionLocale(bool $force = false): void
    {
        $localeKey = self::getRouteSessionKey()->value;
        $locale = Session::get($localeKey);

        if ($force || !Session::has($localeKey)) {
            $locale = self::defaultLocale();
        } else {

            if (!in_array($locale, self::avilableLocales()))
                $locale = self::defaultLocale();
        }

        Session::put($localeKey, $locale);
        App::setLocale($locale);
    }

    /**
     * Get session key based on current route
     *
     * @return \App\Enums\Session\LocaleKeyEnum
     */
    public static function getRouteSessionKey(): self
    {
        return match (RouteTypesEnum::type()) {

            RouteTypesEnum::AdminPanel  => self::AdminPanel,
            RouteTypesEnum::Site        => self::Community,

            default => self::Community
        };
    }

    /**
     * Get avilable locales base on route
     *
     * @return array
     */
    public static function avilableLocales(): array
    {
        return match (self::getRouteSessionKey()) {

            self::AdminPanel      => config('app.available_locales'),
            self::Community       => config('app.available_locales'), // limit exmp: [LocaleEnum::Persian->value],

            default => config('app.available_locales')
        };
    }

    /**
     * Get default locale base on route URI
     *
     * @return string
     */
    public static function defaultLocale(): string
    {
        $sessionLang = LocaleEnum::getNameByValue(Session::get(self::getRouteSessionKey()->value));

        $defLang = match (self::getRouteSessionKey()) {

            self::AdminPanel    => UserSetting::get(AppSettingsEnum::AdminPanelDefaultLanguage, $sessionLang),
            self::Community     => UserSetting::get(AppSettingsEnum::CommunityDefaultLanguage, $sessionLang), // limit exmp: LocaleEnum::Persian->value

            default => null
        };

        return is_null($defLang) ? config('app.locale') : LocaleEnum::getCase($defLang)->value;
    }
    /******************** static methods END ********************/
}
