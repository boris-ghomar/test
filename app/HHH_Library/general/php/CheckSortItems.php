<?php

namespace App\HHH_Library\general\php;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CheckSortItems
{

    protected $modelClass, $request;
    protected $defaultSortField, $defaultSortOrder;
    protected $sortFieldKey, $sortOrderKey;
    protected $sortItems = [];
    /**
     * Undocumented function
     *
     * @param [class] $modelClass
     * @param Request $request
     */
    public function __construct($modelClass, Request $request)
    {
        $this->modelClass = $modelClass;
        $this->request = $request;

        $this->sortFieldKey = Config::get('hhh_config.keywords.sortField');
        $this->sortOrderKey = Config::get('hhh_config.keywords.sortOrder');

        $this->defaultSortField = $modelClass::$defaultSortField;
        $this->defaultSortOrder = strtolower($modelClass::$defaultSortOrder);

        $this->sortItems = [
            $this->sortFieldKey =>  $this->defaultSortField,
            $this->sortOrderKey =>  $this->defaultSortOrder,
        ];
    }


    /**
     * Undocumented function
     *
     * @return array $this->sortItems ( modeified sort array )
     */
    public function getSortItems()
    {
        return $this->sortItems;
    }

    /**
     * This value is received by default from the model class in "__construct",
     *  but you can override it using this function if needed.
     *
     * @param  string $defaultSortField
     * @return void
     */
    public function setDefaultSortField(string $defaultSortField)
    {
        if (!ModelHelper::hasColumn($this->modelClass, $defaultSortField))
            $this->defaultSortField = $defaultSortField;
    }

    /**
     * This value is received by default from the model class in "__construct",
     *  but you can override it using this function if needed.
     *
     * @param  string $defaultSortOrder
     * @return void
     */
    public function setDefaultSortOrder(string $defaultSortOrder)
    {
        $defaultSortOrder = strtolower($defaultSortOrder);

        if ($defaultSortOrder == 'asc' || $defaultSortOrder == 'desc') {
            $this->defaultSortOrder = $defaultSortOrder;
        }
    }

    /**
     * Check all available items
     *
     * @return array $this->getFilter()
     */
    public function checkAll()
    {

        $this->checkSortField();
        $this->checkSortOrder();


        return $this->getSortItems();
    }

    /**
     * Undocumented function
     *
     * @param  string $this->defaultSortField
     * @return void
     */
    public function checkSortField()
    {
        if ($this->request->has($this->sortFieldKey)) {

            $sortField = $this->request->input($this->sortFieldKey);

            if (ModelHelper::hasColumn($this->modelClass, $sortField)) {
                $this->sortItems[$this->sortFieldKey] = $sortField;
            }
        }

        return $this->getSortItems();
    }

    /**
     * Undocumented function
     *
     * @param  string $this->defaultSortOrder
     * @return void
     */
    public function checkSortOrder()
    {
        if ($this->request->has($this->sortOrderKey)) {

            $sortOrder = strtolower($this->request->input($this->sortOrderKey));

            if ($sortOrder == 'asc' || $sortOrder == 'desc') {
                $this->sortItems[$this->sortOrderKey] = $sortOrder;
            }
        }

        return $this->getSortItems();
    }
}
