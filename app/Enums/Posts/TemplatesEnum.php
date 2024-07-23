<?php

namespace App\Enums\Posts;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum TemplatesEnum implements Translatable
{
    use EnumActions;

    case FAQ;
    case Article;
    case PhotoGallery;
    case VideoGallery;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return match ($this) {
            self::FAQ               => __('thisApp.Enum.TemplatesEnum.FAQ'),
            self::Article           => __('thisApp.Enum.TemplatesEnum.Article'),

                // Disabled for now
                // self::PhotoGallery      => __('thisApp.Enum.TemplatesEnum.PhotoGallery'),
                // self::VideoGallery      => __('thisApp.Enum.TemplatesEnum.VideoGallery'),

            default => null
        };
    }
}
