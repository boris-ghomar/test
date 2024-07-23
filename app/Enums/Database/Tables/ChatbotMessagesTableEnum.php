<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum ChatbotMessagesTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case ChatbotChatId;
    case ChatbotStepId;
    case IsBotMessage;
    case Type;
    case Content;
    case IsPassed;

        // Model accessors
    case PhotoUrl;

    /**
     * Register attributes that you want to
     * cast before use (Set OR Get).
     *
     * @return array
     */
    public static function castableAttributes(): array
    {
        return [
            //
        ];
    }

    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        $castableAttributes = self::castableAttributes();
        $dbName = $this->dbName();

        if (array_key_exists($dbName, $castableAttributes)) {

            /** @var CastEnum $castEnum */
            $castEnum = $castableAttributes[$dbName];
            return $castEnum->cast($value);
        }

        return $value;
    }
}
