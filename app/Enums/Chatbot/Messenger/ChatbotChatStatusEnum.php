<?php

namespace App\Enums\Chatbot\Messenger;


use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum ChatbotChatStatusEnum implements Translatable
{
    use EnumActions;

    case Active;
    case Closed;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Site.ChatbotChatsStatus.' . $this->name, [], is_null($locale) ? null : $locale->value);
    }

    /**
     * Get badge class for display in my chats list
     *
     * @return badge-info
     */
    public function getbadge(): string
    {

        return match ($this) {
            self::Active        => "badge-primary",
            self::Closed        => "badge-success",
        };
    }
}
