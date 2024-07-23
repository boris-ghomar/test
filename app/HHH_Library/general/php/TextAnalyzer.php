<?php

namespace App\HHH_Library\general\php;


/**
 * General and usable functions
 *
 *
 */

class TextAnalyzer
{

    private $texts = [];

    function __construct(array|string|null $input)
    {
        $this->setTexts($input);
    }

    /**
     * Set texts
     *
     * @param  array|string|null $input
     * @return void
     */
    public function setTexts(array|string|null $input): void
    {
        if (is_null($input))
            $this->texts = [];

        $this->texts = is_array($input) ? $input : [$input];
    }

    /**
     * Get texts
     *
     * @return array
     */
    public function getTexts(): array
    {
        return $this->texts;
    }

    /**
     * Get a report on the number of words used.
     *
     * @param  bool $getAsJson true ? JSON string : array
     * @return array
     */
    public function getWordsReport(bool $getAsJson = false): array|string
    {
        $words = [];

        foreach ($this->getTexts() as $item) {

            // remove white spaces
            $item = preg_replace('/\s+/', ' ', $item);
            $itemWords = explode(" ", $item);

            foreach ($itemWords as $word) {

                if (!empty($word)) {

                    if (array_key_exists($word, $words))
                        $words[$word]++;
                    else
                        $words[$word] = 1;
                }
            }
        }
        return $getAsJson ? JsonHelper::jsonEncode_UNESCAPED_UNICODE($words) : $words;
    }

    /**
     * Get the word count of the input texts
     *
     * @return int
     */
    public function getWordsCount(): int
    {
        $count = 0;

        $wordsReport = $this->getWordsReport();

        foreach ($wordsReport as $key => $value) {

            $count += $value;
        }

        return $count;
    }
}
