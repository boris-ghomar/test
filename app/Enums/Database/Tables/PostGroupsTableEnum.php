<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;

enum PostGroupsTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case ParentId;
    case Title;
    case Description;
    case Template;
    case Photo;
    case IsSpace;
    case IsPublicSpace;
    case IsActive;
    case Position;
    case PrivateNote;

        // Model attributes aliases
    case DisplayUrl;
    case UrlSlug;
    case CanonicalUrl;

    /**
     * Register attributes that you want to
     * cast before use (Set OR Get).
     *
     * @return array
     */
    public static function castableAttributes(): array
    {
        return [
            self::IsActive->dbName()        => CastEnum::Boolean,
            self::IsSpace->dbName()         => CastEnum::Boolean,
            self::IsPublicSpace->dbName()   => CastEnum::Boolean,
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
