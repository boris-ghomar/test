<?php

namespace App\Enums\Domains;


use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum DomainStatusEnum implements Translatable
{
    use EnumActions;

    case Preparing;
    case ReadyToUse;
    case InUse;
    case Blocked;
    case Expired;
    case Unknown;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        $translateKey = 'thisApp.Enum.DomainStatusEnum.' . $this->name;
        $translation = __($translateKey);

        return $translation == $translateKey ? $this->name : $translation;
    }

    /**
     * Get usable status names
     *
     * @return array
     */
    public static function getUsableStatusNames(): array
    {
        return [
            self::ReadyToUse->name,
            self::InUse->name
        ];
    }

    /**
     * Get expired status names
     *
     * @return array
     */
    public static function getAssignmentExpiredStatusNames(): array
    {
        return [
            self::Blocked->name,
            self::Expired->name
        ];
    }

    /**
     * Check if the domain status is assignable
     *
     * @return bool
     */
    public function isUseable(): bool
    {
        return in_array($this->name, self::getUsableStatusNames());
    }
}
