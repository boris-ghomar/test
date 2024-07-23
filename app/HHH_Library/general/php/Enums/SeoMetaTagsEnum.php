<?php

namespace App\HHH_Library\general\php\Enums;

use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum SeoMetaTagsEnum implements Translatable
{
    use EnumActions;

    case MetaCharset;
    case MetaViewport;
    case MetaRobots;
    case MetaTitle;
    case MetaDescription;
    case Author;
    case Keywords;
    case Canonical;

    /**
     * Get HTML tag of meta tag
     *
     * @param  mixed $value
     * @return ?string
     */
    public function getHtmlTag(?string $value = null): string
    {

        return match ($this) {

            self::MetaCharset       => '<meta charset="UTF-8">',
            self::MetaViewport      => '<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">',
            self::MetaRobots        => sprintf('<meta name="robots" content="%s">', empty($value) ? "index, follow" : $value), // values: index, noindex, follow, nofollow
            self::MetaTitle         => (empty($value)) ? '' : sprintf('<title>%s</title>', $value),
            self::MetaDescription   => (empty($value)) ? '' : sprintf('<meta name="description" content="%s">', $value),
            self::Author            => (empty($value)) ? '' : sprintf('<meta name="author" content="%s">', $value), // Sample: <meta name="author" content="John Doe">
            self::Keywords          => (empty($value)) ? '' : sprintf('<meta name="keywords" content="%s">', $value), // Sample: <meta name="keywords" content="HTML, CSS, JavaScript">
            self::Canonical         => (empty($value)) ? '' : sprintf('<link rel="canonical" href="%s" />', $value), // Sample: <link rel="canonical" href="http://www.example.com/product.html" />

            default => ""
        };
    }

    /**
     * Minimum length of meta tag
     *
     * @return int
     */
    public function minLength(): int
    {
        return match ($this) {

            self::MetaTitle => 40,
            self::MetaDescription => 120,

            default => 0
        };
    }

    /**
     * Maximum length of meta tag
     *
     * @return int
     */
    public function maxLength(): int
    {
        return match ($this) {

            self::MetaTitle => 60,
            self::MetaDescription => 160,

            default => 0
        };
    }

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        $translationNode = sprintf('general.SeoMetaTags.%s.Title', $this->name);
        $translate = __($translationNode);

        return $translate === $translationNode ? $this->name : $translate;
    }

    /**
     * Get item description
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function description(LocaleEnum $locale = null): ?string
    {
        $translationNode = sprintf('general.SeoMetaTags.%s.Description', $this->name);
        return __($translationNode);
    }
}
