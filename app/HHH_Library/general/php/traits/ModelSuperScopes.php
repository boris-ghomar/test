<?php

namespace App\HHH_Library\general\php\traits;

use App\HHH_Library\Calendar\CalendarHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait  ModelSuperScopes
{
    /**
     * Super Scopes can be used in models scope
     */

    protected $reqTableName = null;

    /**
     * get Col FullName:
     * This applies when this table is joined with another table,
     * in which case ambiguity in the SQL will be avoided.
     *
     *
     * @param  string dbCol
     * @param  ?string tableName :If this value is null, the value of the table name of the same model will be used
     * @return string tableName.colName
     */
    public function getColFullName(string $dbCol, ?string $tableName = null): string
    {
        if (Str::of($tableName)->trim()->isEmpty()) {

            if (Str::of($this->reqTableName)->trim()->isEmpty())
                $tableName = $this->getTable();
            else
                $tableName = $this->reqTableName;
        }

        return $tableName . '.' . $dbCol;
    }

    /**
     * Separate column name from table name:
     *
     * if $dbCol == colName => $dbCol = colName;
     * if $dbCol == tableName.colName => $dbCol = colName;
     *
     * @param  string colName || tableName.colName
     * @return string colName
     */
    public function getColName(string $dbCol): string
    {
        // In starter functions this should return to the default state
        $this->reqTableName = null;

        $nameArray = explode('.', $dbCol);
        if (count($nameArray) > 1) {

            $reqTableName = Str::of($nameArray[0])->trim();
            if (!Str::of($reqTableName)->isEmpty())
                $this->reqTableName = $reqTableName;
        }

        return Str::afterLast($dbCol, '.');
    }

    /**
     * This function checks if the input value of the checkbox is bool or not.
     * If the input is bool, this function returns bool value and if it is not boolean, it returns null.
     *
     * @param ?array $filter input data array
     * @param  ?string $filterKey attribute key in filter array
     * @return ?bool null: for not bool value
     */
    protected function getCheckboxInput(?array $filter, ?string $filterKey = null): ?bool
    {
        if ($this->checkInputFilter($filterKey, $filter)) {

            //$value != "undefined"
            $value = $filter[$filterKey];

            if ($value === 1) return true;
            if ($value === 0) return false;

            if (!is_string($value) && !is_bool($value)) {
                return null;
            }

            if (is_string($value)) {

                $value = strtolower($value);

                $allowedInputs = ["true", "false", "1", "0"];

                if (in_array($value, $allowedInputs)) {

                    $value = strtolower($value);
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);;
                } else
                    return null;
            }

            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return null;
    }

    /**
     * This function checks whether the requested key
     * is present in the filter array.
     *
     * @param  ?string $filterKey
     * @param ?array $filter
     * @param bool $acceptNull
     * @return bool
     */
    protected function checkInputFilter(?string $filterKey = null, ?array $filter, bool  $acceptNull = false): bool
    {
        /**
         * Don't use this in this function,because this used in starter functions.
         * $dbCol = $this->getColName($dbCol);
         */

        if (!is_null($filter) && Arr::has($filter, $filterKey)) {

            $value = strtolower($filter[$filterKey]);

            if (!is_null($value) && $value != "null")
                return true;
            else
                return $acceptNull ? true : false;
        }

        return false;
    }


    /**
     * SortOrder function
     *
     * * If you want to sort another item instead of sorting one item,
     * you can use this "$replaceSortFields" array.
     *
     *  example:
     *  $replaceSortFields = ['office_category_id' => 'office_category_name']
     *  now If 'office_category_id' is received for sort field,
     *  'office_category_name' sorts will be used instead
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param ?callable $defaultCallback
     * @param array $replaceSortFields
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function superScopeSortOrder(Builder $query, ?array $filter, ?callable $defaultCallback = null, array $replaceSortFields = []): Builder
    {

        try {
            $sortFieldKey = config('hhh_config.keywords.sortField');
            $sortOrderKey = config('hhh_config.keywords.sortOrder');

            $sortField = Str::of($filter[$sortFieldKey])->trim()->__toString();
            $sortOrder = strtolower(Str::of($filter[$sortOrderKey])->trim());

            if (Arr::has($replaceSortFields, $sortField)) {
                $sortField = Str::of($replaceSortFields[$sortField])->trim();
            }

            return $query->orderBy($sortField, $sortOrder);
        } catch (\Throwable $th) {
            //throw $th;
        }

        return is_null($defaultCallback) ? $query : $defaultCallback($query);
    }



    /**
     * Scope a query to only include '%like%' input filed as request.
     * For find strings  included '%like%' in database
     * such as: name, email, ...
     *
     * @param  ?string $dbCol
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  ?string $filterKey attribute key in filter array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function superScopeLikeAs(?string $dbCol, Builder $query, ?array $filter, ?string $filterKey = null): Builder
    {
        $dbCol = $this->getColName($dbCol);

        if (Str::of($filterKey)->trim()->isEmpty())
            $filterKey = $dbCol;

        if ($this->checkInputFilter($filterKey, $filter)) {

            if (!Str::of($filter[$filterKey])->trim()->isEmpty())
                return $query->where($this->getColFullName($dbCol), 'like', '%' . $filter[$filterKey] . '%');
        }
        return $query;
    }

    /**
     * Scope a query to only include input filed as request in|notIn items.
     * For find records included in|notIn items(array) in database
     *
     * @param  ?string $dbCol
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param ?array $items
     * @param bool $notIn
     * @param  ?string $filterKey attribute key in filter array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function superScopeArray(?string $dbCol, Builder $query, ?array $filter, ?array $items, bool $notIn = false, ?string $filterKey = null): Builder
    {
        if (empty($items) && empty($filter[$filterKey]))
            return $query;

        $dbCol = $this->getColName($dbCol);

        if (Str::of($filterKey)->trim()->isEmpty())
            $filterKey = $dbCol;

        if ($this->checkInputFilter($filterKey, $filter)) {

            if ($notIn)
                return $query->whereNotIn($this->getColFullName($dbCol), $items);
            else
                return $query->whereIn($this->getColFullName($dbCol), $items);
        }
        return $query;
    }


    /**
     * Scope a query to only Exactly include input filed as request.
     * Such as: status['open'|'closed'], gender= ['male' | 'femail']
     *
     *
     * @param  ?string $dbCol
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  ?string $filterKey attribute key in filter array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function superScopeExactly(?string $dbCol, Builder $query, ?array $filter, ?string $filterKey = null): Builder
    {
        $dbCol = $this->getColName($dbCol);

        if (Str::of($filterKey)->trim()->isEmpty())
            $filterKey = $dbCol;

        if ($this->checkInputFilter($filterKey, $filter)) {

            if (!Str::of($filter[$filterKey])->trim()->isEmpty())
                return $query->where($this->getColFullName($dbCol), $filter[$filterKey]);
        }

        return $query;
    }

    /**
     * Scope a query to only Exactly include input filed as request
     * and it must be number, because this is for number input fields..
     *
     *
     *
     * @param  ?string $dbCol
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  ?string $filterKey attribute key in filter array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function superScopeExactlyNumber(?string $dbCol, Builder $query, ?array $filter, ?string $filterKey = null): Builder
    {
        $dbCol = $this->getColName($dbCol);

        if (Str::of($filterKey)->trim()->isEmpty())
            $filterKey = $dbCol;

        if ($this->checkInputFilter($filterKey, $filter)) {

            if ($filter[$filterKey] != "undefined") {

                $filter[$filterKey] = filter_var($filter[$filterKey], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                if (is_numeric($filter[$filterKey]))
                    return $this->superScopeExactly($this->getColFullName($dbCol), $query, $filter, $filterKey);
            }
        }

        return $query;
    }

    /**
     * Scope a query to only Exactly include input filed as request.
     * Such as: parent_id, office_id, ... in Dropboxes
     *
     * Dropboxes use negative keys(-1,-2,...) instead of IDs
     * to create items that do not exist in the database, like the "All" option.
     * That is why the "if ($filter[$dbCol] != $unselectedItemValue)" has been used
     *
     * In the string dropboxes may be use "" for $unselectedItemValue, such as: $unselectedItemValue = ""
     * or you can use other items as $unselectedItemValue
     *
     * @param  ?string $dbCol
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  ?string $filterKey attribute key in filter array
     * @param  string|int $unselectedItemValue
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function superScopeDropbox(?string $dbCol, Builder $query, ?array $filter, ?string $filterKey = null, $unselectedItemValue = ""): Builder
    {
        $dbCol = $this->getColName($dbCol);

        if (Str::of($filterKey)->trim()->isEmpty())
            $filterKey = $dbCol;

        if ($this->checkInputFilter($filterKey, $filter)) {

            if (!is_null($filter[$filterKey]) && $filter[$filterKey] != $unselectedItemValue)
                return $query->where($this->getColFullName($dbCol), $filter[$filterKey]);
        }

        return $query;
    }

    /**
     * In this function as default: $unselectedItemValue = -1 .
     *
     * @param  ?string $dbCol
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  ?string $filterKey attribute key in filter array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function superScopeDropboxId(?string $dbCol, Builder $query, ?array $filter, ?string $filterKey = null, $unselectedItemValue = -1): Builder
    {
        return $this->superScopeDropbox($dbCol, $query, $filter, $filterKey, $unselectedItemValue);
    }

    /**
     * This function filters based on the input value of the checkbox.
     *
     * @param  ?string $dbCol
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  ?string $filterKey attribute key in filter array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function superScopeCheckbox(?string $dbCol, Builder $query, ?array $filter, ?string $filterKey = null): Builder
    {
        $dbCol = $this->getColName($dbCol);

        if (Str::of($filterKey)->trim()->isEmpty())
            $filterKey = $dbCol;

        $value = $this->getCheckboxInput($filter, $filterKey);

        if (!is_null($value)) {

            // convert boolean to int: true => 1 | false => 0
            $value = intval($value);

            return $query->where($this->getColFullName($dbCol), $value);
        }

        return $query;
    }

    /**
     * This function filters based on date range
     *
     * sample of input dateRanage
     * input format: JSON-String (encoded json)
     * {
     *    "fromDate": "1",
     *    "toDate": "2"
     * }
     *
     *
     * @param  ?string $dbCol
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  ?string $filterKey attribute key in filter array
     * @param  App\HHH_Library\Calendar\CalendarHelper $calendarHelper
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function superScopeDateRange(?string $dbCol, Builder $query, ?array $filter, ?string $filterKey = null, CalendarHelper $calendarHelper): Builder
    {
        try {

            $dbCol = $this->getColName($dbCol);

            if (Str::of($filterKey)->trim()->isEmpty())
                $filterKey = $dbCol;

            if ($this->checkInputFilter($filterKey, $filter)) {

                if (Str::of($filter[$filterKey])->trim()->isEmpty())
                    return $query;

                $fromDateKey = config('hhh_config.keywords.fromDate');
                $toDateKey = config('hhh_config.keywords.toDate');

                $dateRanage = json_decode($filter[$filterKey]);
                $fromDate = trim($dateRanage->$fromDateKey);
                $toDate = trim($dateRanage->$toDateKey);

                $fromDate = (Str::of($fromDate)->isEmpty()) ? null : $fromDate;
                $toDate = (Str::of($toDate)->isEmpty()) ? null : $toDate;

                if (empty($fromDate) && empty($toDate))
                    return $query;

                // All times are stored in the database based on the UTC time
                $UTC_fromDate = is_null($fromDate) ? null : $calendarHelper->convertToUTC($fromDate);
                $UTC_toDate   = is_null($toDate) ? null : $calendarHelper->convertToUTC($toDate);

                if (!is_null($UTC_fromDate))
                    $query = $query->where($this->getColFullName($dbCol), '>=', $UTC_fromDate);

                if (!is_null($UTC_toDate))
                    $query = $query->where($this->getColFullName($dbCol), '<=', $UTC_toDate);

                return $query;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $query;
    }

    /**
     * This function filters based on number range
     *
     * sample of input numberRanage
     * input format: JSON-String (encoded json)
     * {
     *    "fromNumber": "1",
     *    "toNumber": "2"
     * }
     *
     *
     * @param  ?string $dbCol
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  ?string $filterKey attribute key in filter array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function superScopeNumberRange(?string $dbCol, Builder $query, ?array $filter, ?string $filterKey = null): Builder
    {
        try {

            $dbCol = $this->getColName($dbCol);

            if (Str::of($filterKey)->trim()->isEmpty())
                $filterKey = $dbCol;

            if ($this->checkInputFilter($filterKey, $filter)) {

                if (Str::of($filter[$filterKey])->trim()->isEmpty())
                    return $query;

                $fromNumberKey = config('hhh_config.keywords.fromNumber');
                $toNumberKey = config('hhh_config.keywords.toNumber');

                $numberRanage = json_decode($filter[$filterKey]);
                $fromNumber = trim($numberRanage->$fromNumberKey);
                $toNumber = trim($numberRanage->$toNumberKey);

                $fromNumber = (Str::of($fromNumber)->isEmpty()) ? null : $fromNumber;
                $toNumber = (Str::of($toNumber)->isEmpty()) ? null : $toNumber;

                if (is_null($fromNumber) && is_null($toNumber))
                    return $query;

                if (!is_null($fromNumber))
                    $query = $query->where($this->getColFullName($dbCol), '>=', $fromNumber);

                if (!is_null($toNumber))
                    $query = $query->where($this->getColFullName($dbCol), '<=', $toNumber);

                return $query;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $query;
    }
}
