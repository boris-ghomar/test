<?php

namespace App\Enums\Settings;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\TranslateRouteName;
use App\Interfaces\Translatable;
use Illuminate\Support\Str;

enum DynamicDataVariablesEnum implements Translatable
{
    use EnumActions;
    use TranslateRouteName;


    case IpRestriction_Explanation;
    case IpRestriction_SiteLink;

    case Comment_CommentRegistrationExplanation;

    case Search_GuideText;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {

        return match ($this) {

            self::IpRestriction_Explanation => $this->transRouteName(['thisApp.Enum.DynamicData.IpRestriction.SectionTitle', 'thisApp.Enum.DynamicData.IpRestriction.Explanation']),
            self::IpRestriction_SiteLink => $this->transRouteName(['thisApp.Enum.DynamicData.IpRestriction.SectionTitle', 'thisApp.Enum.DynamicData.IpRestriction.SiteLink']),

            self::Comment_CommentRegistrationExplanation => $this->transRouteName(['thisApp.Enum.DynamicData.Comment.SectionTitle', 'thisApp.Enum.DynamicData.Comment.CommentRegistrationExplanation']),

            self::Search_GuideText => $this->transRouteName(['thisApp.Enum.DynamicData.Search.SectionTitle', 'thisApp.Enum.DynamicData.Search.SearchGuideText']),

            default => $this->name
        };
    }

    /**
     * Register your logic for before saving
     *
     * @param  mixed $value
     * @return mixed
     */
    public function modifyValue(mixed $value): mixed
    {
        return match ($this) {

            self::IpRestriction_SiteLink => $this->modifySiteLink($value),

            default => $value
        };
    }

    /**
     * Modify site link
     *
     * @param  ?string $siteLink
     * @return ?string
     */
    private function modifySiteLink(?string $siteLink): ?string
    {
        // https://www.ccmcijkgrvjza.click/fa/

        if (empty($siteLink))
            return $siteLink;

        $siteLink = Str::start($siteLink, "https://www.");
        $siteLink = Str::finish($siteLink, "/fa/");

        return $siteLink;
    }
}
