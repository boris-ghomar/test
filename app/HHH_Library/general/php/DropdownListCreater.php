<?php

namespace App\HHH_Library\general\php;

use App\HHH_Library\general\php\Enums\PregPatternValidationEnum;
use Illuminate\Database\Eloquent\Builder;

class DropdownListCreater
{
    /**
     * NOTICE:
     * The replacement of key and text is done so that when dealing with numerical Enums,
     * the assignment of the value does not interfere with the index of the array.
     * If needed, you can use the "useReverseList" function.
     */

    private const TEXT_LABEL = 'text', VALUE_LABEL = 'value';

    private $list = null;
    private $prependList = [];
    private $ascSort = null;
    private $sortText = true;
    private $lable = null;
    private $useReverseList = false;

    // filters
    private $caseSensitive = true;
    private $allowedTexts = null;
    private $allowedValues = null;
    private $notAllowedTexts = null;
    private $notAllowedValues = null;

    /**
     * Make drop-down list by array list
     *
     * @param  array $list Types: [$item1, $item2, ...] OR [$text1 => $value1, $text2 => $value2, ...]
     * @return self
     */
    public static function makeByArray(array $list): self
    {
        return (new self())->setList($list);
    }

    /**
     * Make drop-down list by model
     *
     * @param  string $modelClass
     * @param  string $column
     * @param  string $primaryKey
     * @return self
     */
    public static function makeByModel(string $modelClass, string $column, string $primaryKey = "id"): self
    {
        $items = $modelClass::select($primaryKey, $column)
            ->groupby($column)->distinct()
            ->get()
            ->toArray();

        $instance = (new self());

        $list = $instance->removeLabels($items, $column, $primaryKey);

        return $instance->setList($list);
    }

    /**
     * Make drop-down list by model query builder
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $column
     * @param  string $primaryKey
     * @return self
     */
    public static function makeByModelQuery(Builder $query, string $column, string $primaryKey = "id"): self
    {
        $items = $query->select($primaryKey, $column)
            ->groupby($column)->distinct()
            ->get()
            ->toArray();

        $instance = (new self());

        $list = $instance->removeLabels($items, $column, $primaryKey);

        return $instance->setList($list);
    }

    /**
     * Get drop-down list
     *
     * @return array
     */
    public function get(): array
    {
        $list = $this->list;

        #1 Validate list
        if (!$this->validateList()) return [];

        #2 Conver list to drop-down list
        $list = $this->convertListToDropdownList($list);

        #3 Reverse list items if need
        $list = $this->reverseList($list);

        #4 Filter list
        $list = $this->filter($list);

        #5 Sort list
        $list = $this->sortList($list);

        #6 Prepend list
        $list = $this->prependItems($list);

        #7 Add Labels
        $list = $this->addLabels($list);

        return $list;
    }


    /**
     * Get json list
     *
     * @param  bool $pretty
     * @return string
     */
    public function getJson(bool $pretty = false): string
    {
        return $pretty ? json_encode($this->get(), JSON_PRETTY_PRINT) : json_encode($this->get());
    }

    /**
     * Set list
     *
     * @param  array $list
     * @return self
     */
    public function setList(array $list): self
    {
        $this->list = $list;
        return $this;
    }

    /**
     * Use this function to reverse the key and text position.
     *
     * @return self
     */
    public function useReverseList(): self
    {
        $this->useReverseList = true;
        return $this;
    }

    /**
     * Prepend item to list
     *
     * @param  string $text
     * @param  mixed $value
     * @return self
     */
    public function prepend(string $text, mixed $value): self
    {
        $this->prependList[$text] = $value;
        return $this;
    }

    /**
     * Set sort order
     *
     * @param  mixed $ascSort (optinal) null: no sort , true: asc sort, false: desc sort
     * @param bool $sortText (For text=>value arrays), Default: sort text
     * @return self
     */
    public function sort(bool|null $ascSort = true, bool $sortText = true): self
    {
        $this->ascSort = $ascSort;
        $this->sortText = $sortText;
        return $this;
    }

    /**
     * Use lable for text and value
     * If use label the result will be returns as below sampel:
     *  [
     *      ['name' => $text1 , 'id' => $value1],
     *      ['name' => $text2 , 'id' => $value2],
     *      ...
     *  ]
     *
     * @param  mixed $textLabel Display name
     * @param  mixed $valueLabel The value returned for the selected item
     * @return self
     */
    public function useLable(string $textLabel = 'name', string $valueLabel = 'id'): self
    {
        $this->lable[self::TEXT_LABEL]   = $textLabel;
        $this->lable[self::VALUE_LABEL] = $valueLabel;
        return $this;
    }

    /**
     * set case sensitive filter
     *
     * @param  bool $caseSensitive
     * @return self
     */
    public function caseSensitiveFilter(bool $caseSensitive): self
    {
        $this->caseSensitive = $caseSensitive;
        return $this;
    }

    /**
     * Set allowed display texts.
     * The dropdown will only add items with allowed display texts to the list
     *
     * @param  ?array $allowedTexts
     * @return self
     */
    public function allowedTexts(?array $allowedTexts): self
    {
        $this->allowedTexts = $this->filterValue($allowedTexts);
        return $this;
    }

    /**
     * Set allowed values.
     * The dropdown will only add items with allowed values to the list
     *
     * @param  ?array $allowedTexts
     * @return self
     */
    public function allowedValues(?array $allowedValues): self
    {
        $this->allowedValues = $this->filterValue($allowedValues);
        return $this;
    }

    /**
     * Set not allowed display texts.
     * The drop-down will remove items with these display texts from the list
     *
     * @param  ?array $allowedTexts
     * @return self
     */
    public function notAllowedTexts(?array $notAllowedTexts): self
    {
        $this->notAllowedTexts = $this->filterValue($notAllowedTexts);
        return $this;
    }

    /**
     * Set not allowed values.
     * The drop-down will remove items with these values from the list
     *
     * @param  ?array $allowedTexts
     * @return self
     */
    public function notAllowedValues(?array $notAllowedValues): self
    {
        $this->notAllowedValues = $this->filterValue($notAllowedValues);
        return $this;
    }

    /**
     * Get only items that display texts are used in the model column
     *
     * @param  ?string $modelClass
     * @param  ?string $column
     * @return self
     */
    public function onlyUsedTextsInModel(?string $modelClass, ?string $column): self
    {
        return $this->onlyUsedInModel($modelClass, $column, true, true);
    }

    /**
     * Get only items that values are used in the model column
     *
     * @param  ?string $modelClass
     * @param  ?string $column
     * @return self
     */
    public function onlyUsedValuesInModel(?string $modelClass, ?string $column): self
    {
        return $this->onlyUsedInModel($modelClass, $column, true, false);
    }

    /**
     * Get only items that display texts are not used in the model column
     *
     * @param  ?string $modelClass
     * @param  ?string $column
     * @return self
     */
    public function onlyNotUsedTextsInModel(?string $modelClass, ?string $column): self
    {
        return $this->onlyUsedInModel($modelClass, $column, false, true);
    }

    /**
     * Get only items that values are not used in the model column
     *
     * @param  ?string $modelClass
     * @param  ?string $column
     * @return self
     */
    public function onlyNotUsedValuesInModel(?string $modelClass, ?string $column): self
    {
        return $this->onlyUsedInModel($modelClass, $column, false, false);
    }

    /****************************** private methods ******************************/

    /**
     * Validate input lists
     *
     * @return bool
     */
    private function validateList(): bool
    {
        $list = $this->list;
        $prependList = $this->prependList;

        $listCount = is_null($list) ? 0 : count($list);
        $prependListCount = is_null($prependList) ? 0 : count($prependList);

        return !($listCount + $prependListCount === 0);
    }

    /**
     * This function reverses the position of key and text.
     *
     * @param  array $list
     * @return array
     */
    private function reverseList(array $list): array
    {
        if ($this->useReverseList) {

            $res = [];

            if (array_is_list($list)) {

                $index = 1;
                foreach ($list as $item) {
                    $res[$item] = $index;
                    $index++;
                }
            } else {
                foreach ($list as $key => $value) {
                    $res[$value] = $key;
                }
            }

            return $res;
        }

        return $list;
    }

    /**
     * Sort list
     *
     * @param  array $list
     * @return array
     */
    private function sortList(array $list): array
    {
        if (!is_null($this->ascSort)) {

            $persianSortFunc = __CLASS__ . '::persianSort';
            $persianSortReverseFunc = __CLASS__ . '::persianSortReverse';

            if (array_is_list($list)) {

                if ($this->isPersianArray($list))
                    $this->ascSort ? uasort($list, $persianSortFunc) : uasort($list, $persianSortReverseFunc);
                else
                    $this->ascSort ? asort($list) : arsort($list);

                return array_values($list);
            } else {
                if ($this->sortText) {

                    if ($this->isPersianArray(array_keys($list)))
                        $this->ascSort ? uksort($list, $persianSortFunc) : uksort($list, $persianSortReverseFunc);
                    else
                        $this->ascSort ? ksort($list) : krsort($list);
                } else {

                    if ($this->isPersianArray(array_values($list)))
                        $this->ascSort ? uasort($list, $persianSortFunc) : uasort($list, $persianSortReverseFunc);
                    else
                        $this->ascSort ? asort($list) : arsort($list);
                }
            }
        }

        return $list;
    }

    /**
     * Convert list array to drop-down array list.
     * If the array is a list, the item will uses in both text and value.
     *
     *  Sample:
     *  ['item1', 'item2'] => ['item1' => 'item1', 'item2' => 'item2']
     *
     * @param  array $list
     * @return array
     */
    private function convertListToDropdownList(array $list): array
    {
        if (array_is_list($list)) {

            $dropwonList = [];
            foreach ($list as $item) {
                $dropwonList[$item] = $item;
            }
            return $dropwonList;
        }

        return $list;
    }

    /**
     * Prepend required items to drop-down list
     *
     * @param  array $list
     * @return array
     */
    private function prependItems(array $list): array
    {

        if (count($this->prependList) > 0) {
            $list = (count($this->prependList) > 1 || count($list) > 1) ? array_merge($this->prependList, $list) : $list;
        }

        return $list;
    }

    /**
     * Add labels to list items, if need
     *
     * Sample output:
     *  [
     *      ['text' => $text1 , 'value' => $value1],
     *      ['text' => $text2 , 'value' => $value2],
     *      ...
     *  ]
     *
     * @param  array $list
     * @return array
     */
    private function addLabels(array $list): array
    {
        if (!is_null($this->lable)) {

            $LabeledList = [];
            $textLabel   = $this->lable[self::TEXT_LABEL];
            $valueLabel = $this->lable[self::VALUE_LABEL];

            foreach ($list as $text => $value) {

                array_push($LabeledList, [$textLabel => $text, $valueLabel => $value]);
            }

            return  $LabeledList;
        }

        return $list;
    }

    /**
     * Remove labels from list
     *
     * @param  array $list
     * @param  string $textLabel
     * @param  string $valueLabel
     * @return array
     */
    private function removeLabels(array $list, string $textLabel, string $valueLabel): array
    {
        $flatList = [];

        foreach ($list as $item) {

            $flatList[$item[$textLabel]] = $item[$valueLabel];
        }

        return $flatList;
    }

    /**
     * set the true value to filter.
     *
     * @param  ?array $filter
     * @return ?array
     */
    private function filterValue(?array $filter): ?array
    {
        if (is_null($filter))
            return null;
        else if (!array_is_list($filter))
            return null;
        else
            return $filter;
    }

    /**
     * Filter list items base on limits.
     *
     * @param  array $list
     * @return array
     */
    private function filter(array $list): array
    {
        $list = $this->dedicatedFilterList($list, $this->allowedTexts, true, true);
        $list = $this->dedicatedFilterList($list, $this->allowedValues, true, false);
        $list = $this->dedicatedFilterList($list, $this->notAllowedTexts, false, true);
        $list = $this->dedicatedFilterList($list, $this->notAllowedValues, false, false);

        return $list;
    }

    /**
     * Filter list items based on dedicated limit.
     *
     * @param  array $list
     * @param  ?array $filterList
     * @param  bool $allowedType true ? under filter must be in $filterlist : under filter must not be in $filterlist
     * @param  bool $filterByText true ? uses text to filter : uses value to filter
     * @return array
     */
    private function dedicatedFilterList(array $list, ?array $filterList, bool $allowedType, bool $filterByText): array
    {

        if (!is_null($filterList)) {

            if (!$this->caseSensitive)
                $filterList = array_map('strtolower', $filterList);

            $filteredList = [];
            foreach ($list as $text => $value) {

                $needle = $filterByText ? $text : $value;
                $needle = $this->caseSensitive ? $needle : strtolower($needle);

                $addItem = $allowedType ? in_array($needle, $filterList) : !in_array($needle, $filterList);

                if ($addItem)
                    $filteredList[$text] = $value;
            }

            return $filteredList;
        }

        return $list;
    }

    /**
     * Get only items that are used in the model column
     *
     * @param  ?string $modelClass
     * @param  ?string $column
     * @param  bool $allowedType true ? under filter must be in $filterlist : under filter must not be in $filterlist
     * @param  bool $filterByText true ? uses text to filter : uses value to filter
     * @return self
     */
    private function onlyUsedInModel(?string $modelClass, ?string $column, bool $allowedType, bool $filterByText): self
    {
        if (ModelHelper::hasColumn($modelClass, $column)) {

            $filterList = $modelClass::select($column)
                ->groupby($column)->distinct()
                ->pluck($column)->toArray();

            if (count($filterList) > 0) {

                if ($filterByText) {
                    if ($allowedType)
                        $this->allowedTexts($filterList);
                    else
                        $this->notAllowedTexts($filterList);
                } else {
                    if ($allowedType)
                        $this->allowedValues($filterList);
                    else
                        $this->notAllowedValues($filterList);
                }
            }
        }

        return $this;
    }

    /**
     * Check if array is persian
     *
     * @param  array $arr
     * @return bool
     */
    private function isPersianArray(array $arr): bool
    {
        $text = implode(" ", $arr);

        return PregPatternValidationEnum::PersianString->validate($text);
    }
    /****************************** private methods END ******************************/

    /****************************** static methods ******************************/

    const PERSIAN_CARACTERS = [
        1 =>  'ا',
        2 =>  'ب',
        3 =>  'پ',
        4 =>  'ت',
        5 =>  'ث',
        6 =>  'ج',
        7 =>  'چ',
        8 =>  'ح',
        9 =>  'خ',
        10 =>  'د',
        11 =>  'ذ',
        12 =>  'ر',
        13 =>  'ز',
        14 =>  'ژ',
        15 => 'س',
        16 => 'ش',
        17 =>  'ص',
        18 =>  'ض',
        19 =>  'ط',
        20 =>  'ظ',
        21 =>  'ع',
        22 =>  'غ',
        23 =>  'ف',
        24 =>  'ق',
        25 =>  'ک',
        26 =>  'گ',
        27 =>  'ل',
        28 =>  'م',
        29 =>  'ن',
        30 =>  'و',
        31 =>  'ه',
        32 =>  'ی',
    ];

    /**
     * Persian alphabet sort asc
     *
     * @param  mixed $item1
     * @param  mixed $item2
     * @return mixed
     */
    static function persianSort(mixed $item1, mixed $item2): mixed
    {
        if (substr($item1, 0, 2) == substr($item2, 0, 2))
            return call_user_func(__CLASS__ . '::persianSort', substr($item1, 2), substr($item2, 2));
        return array_search(substr($item1, 0, 2), self::PERSIAN_CARACTERS) < array_search(substr($item2, 0, 2), self::PERSIAN_CARACTERS) ? -1 : 1;
    }

    /**
     * Persian alphabet sort desc
     *
     * @param  mixed $item1
     * @param  mixed $item2
     * @return mixed
     */
    static function persianSortReverse(mixed $item1, mixed $item2): mixed
    {
        return call_user_func(__CLASS__ . '::persianSort', $item2, $item1);
    }
    /****************************** static methods END ******************************/
}
