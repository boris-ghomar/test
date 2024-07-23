<?php

namespace App\Enums\Users;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ContactMethodsEnum
{
    use EnumActions;

    case PhoneCall;
    case WhatsAppCall;
    case WhatsAppChat;
    case TelegramChat;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return (__('general.ContactMethodsEnum.' . $this->name, [], is_null($locale) ? null : $locale->value));
    }
}
