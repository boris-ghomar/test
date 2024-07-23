<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;
use App\Interfaces\Castable;
use Illuminate\Support\Str;

enum PostsTableEnum implements Castable
{
    use EnumToDatabaseColumnName;

    case Id;
    case PostSpaceId;
    case Title;
    case Content;
    case MainPhoto;
    case MetaDescription;
    case IsPublished;
    case AuthorId;
    case EditorId;
    case PrivateNote;
    case IsPinned;
    case PinNumber;
    case ContentUpdatedAt;
    case Views;

        // Below items do not stored in the database, it is only model alias attributes
    case MainPhotoUrl;
    case ShortenedContentForTable;
    case ShortenedContentForPostSpace;
    case DisplayUrl; // Used in post model and extended model from Post model
    case DisplayUrlArticle; // used in Post model
    case DisplayUrlFaq; // used in Post model


    /**
     * Register attributes that you want to
     * cast before use (Set OR Get).
     *
     * @return array
     */
    public static function castableAttributes(): array
    {
        return [
            self::IsPublished->dbName()  => CastEnum::Boolean,
        ];
    }

    /**
     * Summarize
     *
     * @param  ?string $value
     * @param  int $length (optional) For use defaults config leave it blank
     * @return string
     */
    public function summarize(?string $value, int $words = 0): string
    {
        if (empty($value))
            return "";

        if ($words < 1) {
            // use default values
            $words = match ($this) {
                self::Content => 30,

                default => 20
            };
        }

        return Str::words($value, $words);
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
