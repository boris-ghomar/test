<?php

namespace App\HHH_Library\general\php\Enums;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum PregPatternEnum: string
{

    use EnumActions;

    case WhiteSpaces                    = '/\s+/'; // All white spaces: " ", "  ", "\t", "\n", etc
    case UrlEncodeSpecialCharacters     = '/[^\p{L}\p{N}]/u';
    case Quotes                         = '/[\"\']/';

    /**
     * Prepare subject for replace.
     * Register the logic for cases that must be prepared before replacement
     *
     * @param array|string $subject
     * @return array|string|null
     */
    private function prepareForReplace(array|string $subject): array|string|null
    {
        return match ($this) {

            self::UrlEncodeSpecialCharacters => self::Quotes->pregReplace("", $subject), // Remove quotes from url

            default => $subject
        };
    }

    /**
     * Perform a regular expression search and replace
     *
     * @param  string|string[] $replacement
     * @param  string|string[] $subject
     * @param int $limit
     * [optional] The maximum possible replacements for each pattern in each subject string. Defaults to -1 (no limit).
     * @return string|string[]|null
     * preg_replace returns an array if the subject parameter is an array, or a string otherwise.
     */
    public function pregReplace(array|string $replacement, array|string $subject, int $limit = -1): array|string|null
    {

        return preg_replace($this->value, $replacement, $this->prepareForReplace($subject), $limit);
    }
}
