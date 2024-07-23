<?php

namespace App\HHH_Library\jsGrid;

use App\Http\Controllers\Controller;

/**
 * Based on:
 * http://js-grid.com/
 * http://js-grid.com/docs
 */


//Global Consts


class jsGrid_Controller extends Controller
{

    const
        jsGridType_Validation   = 0,
        jsGridType_Insert       = 1,
        jsGridType_Edit         = 2,
        jsGridType_Delete       = 3,
        jsGridType_StaticDelete = 4,
        jsGridType_Filter       = 5,
        jsGridType_Static       = 6,
        jsGridType_InsertEdit   = 7,
        jsGridType_InsertDelete = 8,
        jsGridType_EditDelete   = 9;

    /**********************************************************************************************************/
    // jsGrid config items

    // Not boolean const data
    const
        grid_width = "width", grid_height = "height",  grid_pageSize = "pageSize",
        grid_pageButtonCount = "pageButtonCount", grid_deleteConfirm = "deleteConfirm",
        grid_controller = "controller", grid_data = "data", grid_pageLoading = "pageLoading",
        grid_tableClass = "tableClass";

    const grid_fields = "fields"; // fields control item

    // Boolean const data
    const
        grid_isFiltering = "filtering", grid_isEditing = "editing", grid_isInserting = "inserting",
        grid_isSorting = "sorting", grid_isPaging = "paging", grid_isAutoload = "autoload";


    // jsGrid config items END
    /**********************************************************************************************************/



    protected $jsGridConfig = [];
    protected $jsGrid_VariableNames = [];
    protected $jsGrid_Data = [];

    protected $fieldMaker_FiledControl_Properties = [];

    /**
     * @param  string $jsGrid_ContainerId >ID of jsGrid_Container used to display the table
     */
    public function __construct($jsGrid_ContainerId = "jsGrid", $gridType = self::jsGridType_Validation)
    {
        /**
         * Ensure Register "HHH_Library" namespace in your AppServiceProvider in the boot() method
         * app\Providers\AppServiceProvider.php
         *
         * view()->addNamespace("HHH_Library", app_path("HHH_Library"));
         */
        $this->jsGrid_VariableNames = [

            "ContainerId"       =>  $jsGrid_ContainerId,
            "dbName"            =>  $jsGrid_ContainerId . "_db",
            "apiBaseUrl"        =>  null,
            "apiSubUrl"         =>  null,
        ];

        $this->setupDefaultViewDetails($gridType);
    }



    /**
     * Show the form for creating a new resource.
     *@param array $rowsData    Contains database information to be displayed in the table
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ViewParamData = [
            "jsGridConfig"          => $this->jsGridConfig,
            "jsGrid_VariableNames"  =>  $this->jsGrid_VariableNames,
            "data"                  =>  $this->jsGrid_Data,
        ];

        return view("HHH_Library::jsGrid/js_grid", $ViewParamData);
    }

    /********************* setup face functions **************************/
    public function setupDefaultViewDetails($gridType)
    {
        $this->setupDefaultView();

        switch ($gridType) {

            case self::jsGridType_Validation:
                $this->OptimizedFor_Validation();
                break;
            case self::jsGridType_Insert:
                $this->OptimizedFor_Insert();
                break;
            case self::jsGridType_Edit:
                $this->OptimizedFor_Edit();
                break;
            case self::jsGridType_Delete:
                $this->OptimizedFor_Delete();
                break;
            case self::jsGridType_StaticDelete:
                $this->OptimizedFor_StaticDelete();
                break;
            case self::jsGridType_Filter:
                $this->OptimizedFor_Filter();
                break;
            case self::jsGridType_Static:
                $this->OptimizedFor_Static();
                break;
            case self::jsGridType_InsertEdit:
                $this->OptimizedFor_InsertEdit();
                break;
            case self::jsGridType_InsertDelete:
                $this->OptimizedFor_InsertDelete();
                break;
            case self::jsGridType_EditDelete:
                $this->OptimizedFor_EditDelete();
                break;
            default:
                $this->OptimizedFor_Validation();
                break;
        }

        $this->jsGridConfig[$this::grid_fields] = [];
    }

    public function setupDefaultView()
    {

        $this->jsGridConfig = [
            $this::grid_tableClass        => "hhh_jsgridTable",
            $this::grid_height             => "auto", //500
            $this::grid_width              => "100%",
            $this::grid_isFiltering        => true,
            $this::grid_isEditing          => true,
            $this::grid_isInserting        => true,
            $this::grid_isSorting          => true,
            $this::grid_isPaging           => true,
            $this::grid_isAutoload         => true,
            $this::grid_pageSize           => 15,
            $this::grid_pageButtonCount    => 5,
            $this::grid_deleteConfirm      => "Do you really want to delete this item?", // This is a reminder example and you can override this value
            $this::grid_controller         => $this->markAsJavaCode($this->jsGrid_VariableNames['dbName']),
            $this::grid_data               => $this->markAsJavaCode($this->jsGrid_VariableNames['dbName'] . ".rowsData"),
            $this::grid_pageLoading        => true,
            //test
        ];
    }


    public function OptimizedFor_Validation()
    {
    }

    public function OptimizedFor_Insert()
    {
        $this->setItemProperties(self::grid_isEditing, false);

        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showDeleteButton] = false;
        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showEditButton] = false;
    }

    public function OptimizedFor_Edit()
    {
        $this->setItemProperties(self::grid_isInserting, false);

        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showDeleteButton] = false;
    }

    public function OptimizedFor_Delete()
    {
        $this->setItemProperties(self::grid_isInserting, false);
        $this->setItemProperties(self::grid_isEditing, false);

        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showEditButton] = false;
    }

    public function OptimizedFor_StaticDelete()
    {
        $this->setItemProperties(self::grid_isInserting, false);
        $this->setItemProperties(self::grid_isEditing, false);
        $this->setItemProperties(self::grid_isFiltering, false);

        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showEditButton] = false;
    }

    public function OptimizedFor_Filter()
    {
        $this->setItemProperties(self::grid_isInserting, false);
        $this->setItemProperties(self::grid_isEditing, false);

        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showDeleteButton] = false;
        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showEditButton] = false;
    }

    public function OptimizedFor_Static()
    {
        $this->setItemProperties(self::grid_isInserting, false);
        $this->setItemProperties(self::grid_isEditing, false);
        $this->setItemProperties(self::grid_isSorting, false);
        $this->setItemProperties(self::grid_isFiltering, false);

        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showDeleteButton] = false;
        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showEditButton] = false;
    }

    public function OptimizedFor_InsertEdit()
    {
        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showDeleteButton] = false;
    }

    public function OptimizedFor_InsertDelete()
    {
        $this->setItemProperties(self::grid_isEditing, false);

        $this->fieldMaker_FiledControl_Properties[jsGrid_FieldMaker::fieldControl_showEditButton] = false;
    }

    public function OptimizedFor_EditDelete()
    {
        $this->setItemProperties(self::grid_isInserting, false);
    }




    /********************* setup face functions END **************************/


    /********************* setup callback functions **************************/

    /********************* setup callback functions END **************************/


    /********************* Setter & Getter functions **************************/

    /**
     * Samples:
     * grid_height : auto | 100% | 500px
     * grid_width : auto | 100% | 500px
     * @param  string $grid_key
     * @param  string $value
     */
    public function setItemProperties($grid_key = jsGrid_Controller::grid_height, $value = "auto")
    {
        if ($grid_key == $this::grid_data) {
        } else
            $this->jsGridConfig[$grid_key] = $value;
    }

    /**
     * @param const string $grid_key >sample: grid_height
     */
    public function getItem($grid_key = jsGrid_Controller::grid_height)
    {
        $this->jsGridConfig[$grid_key];
    }

    public function putField(jsGrid_FieldMaker $FieldMaker)
    {
        $newField = $FieldMaker->getField();

        if ($newField[$FieldMaker::field_Type] == "select") {

            $this->jsGrid_Data[$newField[$FieldMaker::field_Name]] = $newField['items'];
            $newField['items'] = $this->markAsJavaCode(sprintf("%s.%s", $this->jsGrid_VariableNames['dbName'], $newField[$FieldMaker::field_Name]));
        } else if ($newField[$FieldMaker::field_Type] == "control") {

            foreach ($this->fieldMaker_FiledControl_Properties as $key => $value) {
                $newField[$key] = $value;
            }
        }

        array_push($this->jsGridConfig[$this::grid_fields], $newField);
    }

    /**
     * Undocumented function
     *
     * @param  string $apiBaseUrl
     * @return void
     */
    public function setApiBaseUrl($apiBaseUrl)
    {
        $this->jsGrid_VariableNames['apiBaseUrl'] = $apiBaseUrl;
    }

    public function setApiSubUrl($apiSubUrl)
    {
        $this->jsGrid_VariableNames['apiSubUrl'] = $apiSubUrl;
    }
    /********************* Setter & Getter functions EN **************************/

    /********************* private functions  **************************/
    public static function markAsJavaCode($value)
    {
        return sprintf("hhh_java(%s)", $value);
    }
    /********************* private functions END **************************/
}
