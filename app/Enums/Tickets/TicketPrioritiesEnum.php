<?php

namespace App\Enums\Tickets;


use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum TicketPrioritiesEnum implements Translatable
{
    use EnumActions;

    case Critical;
    case High;
    case Normal;
    case Low;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Site.TicketPrioritiesEnum.' . $this->name, [], is_null($locale) ? null : $locale->value);
    }

    /**
     * Get badge class for display in my chats list
     *
     * @return badge-info
     */
    public function getbadge(): string
    {

        return match ($this) {
            self::Critical  => "badge-danger",
            self::High      => "badge-warning",
            self::Normal    => "badge-primary",
            self::Low       => "badge-success",
        };
    }
}
