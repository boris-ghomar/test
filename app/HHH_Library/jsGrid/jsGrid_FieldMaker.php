<?php

namespace App\HHH_Library\jsGrid;

/**
 * Based on:
 * http://js-grid.com/
 * http://js-grid.com/docs
 */

class jsGrid_FieldMaker
{

    protected array $Field;

    /**********************************************************************************************************/
    // jsGrid field config items
    const
        field_Type = "type", field_Name = "name", field_Title = "title",
        field_Align = "align", field_Width = "width", field_Validate = "validate";

    const
        field_css = "css", fieldCss_Header = "headercss", fieldCss_Filter = "filtercss",
        fieldCss_Insert = "insertcss", fieldCss_Edit = "editcss";

    const
        field_isFiltering = "filtering", field_isInserting = "inserting", field_isEditing = "editing",
        field_isVisible = "visible", field_isSorting = "sorting", field_Sorter = "sorter";

    //For special items
    const
        field_isReadOnly = "readOnly",
        field_isAutosearch = "autosearch", // triggers searching when the user presses `enter` key in the filter input
        field_dateViewType = "dateViewType";

    //Custom properties for filed control
    const
        fieldControl_showEditButton = 'editButton',                 // show edit button
        fieldControl_showDeleteButton = 'deleteButton',             // show delete button
        fieldControl_showClearFilterButton = 'clearFilterButton',   // show clear filter button
        fieldControl_showModeSwitchButton = 'modeSwitchButton';     // show switching filtering/inserting button


    const
        /**
         * You can replace the date format (yyyy-mm-dd) with your own format,
         *  For example: You can use "CustomFormat:dd-mm-yy"
         */
        dateViewType_CustomFormat = "CustomFormat:yy-mm-dd",

        dateViewType_DateString = "toDateString", dateViewType_ISOString = "toISOString", dateViewType_LocaleDateString = "toLocaleDateString",
        dateViewType_LocaleString = "toLocaleString", dateViewType_LocaleTimeString = "toLocaleTimeString", dateViewType_String = "toString",
        dateViewType_TimeString = "toTimeString", dateViewType_UTCString = "toUTCString",

        dateViewType_getFullYear = "getFullYear", // Get the year as a four digit number (yyyy)
        dateViewType_getMonth = "getMonth", // Get the month as a number (0-11)
        dateViewType_getDate = "getDate", // Get the day as a number (1-31)
        dateViewType_getHours = "getHours", // Get the hour (0-23)
        dateViewType_getMinutes = "getMinutes", // Get the minute (0-59)
        dateViewType_getSeconds = "getSeconds", // Get the second (0-59)
        dateViewType_getMilliseconds = "getMilliseconds", // Get the millisecond (0-999)
        dateViewType_getTime = "getTime", // Get the time (milliseconds since January 1, 1970)
        dateViewType_getDay = "getDay"; // Get the weekday as a number (0-6)
    // jsGrid field config items END
    /**********************************************************************************************************/

    /**
     * Constructor
     *
     * @param  ?string $name      The name used in database
     * @param  ?string $title     The name to be used as displaying in table
     * @param string|null $subTitle
     * @return void
     */
    public function __construct(?string $name, ?string $title, ?string $subTitle = null)
    {
        if (!is_null($subTitle))
            $title = sprintf('%s<br><small class="jsgrid-header-sub-text" >%s</small>', $title, $subTitle);

        /**
         * These items (and methods internal items) are set according to the most usage,
         * but you can rewrite each one
         */
        $this->Field = [

            self::field_Type               =>  null,
            self::field_Name               =>  $name,
            self::field_Title              =>  $title,
            self::field_Align              =>  null,   // center | right | left
            self::field_Width              =>  200, // examples: "auto" | "50%" | "150" //The best way to measure pixels: example 150
            self::field_isVisible          =>  true,

            self::field_css                =>  null,
            self::fieldCss_Header          =>  null,
            self::fieldCss_Filter          =>  null,
            self::fieldCss_Insert          =>  null,
            self::fieldCss_Edit            =>  null,

            self::field_isFiltering        =>  true,
            self::field_isInserting        =>  true,
            self::field_isEditing          =>  true,
            self::field_isSorting          =>  true,
            self::field_Sorter             =>  null,

            self::field_Validate           =>  null,

        ];
    }

    /**
     * setSubTitle
     *
     * @param  mixed $subTitle
     * @return void
     */
    public function setSubTitle(string $subTitle): void
    {
    }

    /**
     * Get field details
     *
     * @return array
     */
    public function getField(): array
    {
        return array_filter($this->Field, fn ($value) => !is_null($value) && $value !== '');
    }


    /*********************************************** Field Properties ***********************************************************/
    /**
     * Set custom properties for item.
     *
     * @param [const string]  $field_key :: field_X
     * @param  mixed $value
     * @return void
     */
    public function setItemProperties(string $field_key, mixed $value): void
    {
        $this->Field[$field_key] = $value;
    }

    /** */
    const ProCol_ShowOnly = 1, ProCol_Unstorageable = 2, ProCol_Hidden = 3;

    /**
     * Set custom properties collection for item.
     *
     * @param int $proCol_key
     * @return void
     */
    public function setPropertiesCollection(int $proCol_key): void
    {

        switch ($proCol_key) {

            case self::ProCol_ShowOnly:
                /*This is a set of properties for fields that are only for display, such as a "row" */
                $this->setItemProperties(self::field_isFiltering, false);
                $this->setItemProperties(self::field_isInserting, false);
                $this->setItemProperties(self::field_isEditing, false);
                $this->setItemProperties(self::field_isAutosearch, false);
                $this->setItemProperties(self::field_isReadOnly, true);
                break;

            case self::ProCol_Unstorageable:
                /*This is a set of properties for fields that are only for display and Filtering, such as a "uneditable parent_id" */
                $this->setItemProperties(self::field_isInserting, false);
                $this->setItemProperties(self::field_isEditing, false);
                break;

            case self::ProCol_Hidden:
                /*This is a set of features for fields that are hidden
                but need to be searched and should be considered when
                sending to the server, such as a "id" */
                $this->setItemProperties(self::field_isFiltering, false);
                $this->setItemProperties(self::field_isInserting, false);
                $this->setItemProperties(self::field_isEditing, false);
                $this->setItemProperties(self::field_isAutosearch, false);
                $this->setItemProperties(self::field_isSorting, false);
                $this->setItemProperties(self::field_Sorter, null);
                $this->setItemProperties(self::field_isReadOnly, true);
                $this->setItemProperties(self::field_isVisible, false);
                break;

            default:
                # code...
                break;
        }
    }
    /*********************************************** Field Properties END ***********************************************************/


    /*********************************************** Make Field ***********************************************************/

    /**
     *  Control field renders delete and editing buttons in data row,
     *  search and add buttons in filter and inserting row accordingly.
     *  It also renders button switching between filtering and searching in header row.
     *
     * Custom properties:
     * {
     * editButton: true,                               // show edit button
     * deleteButton: true,                             // show delete button
     * clearFilterButton: true,                        // show clear filter button
     * modeSwitchButton: true,                         // show switching filtering/inserting button

     * align: "center",                                // center content alignment
     * width: 50,                                      // default column width is 50px
     * filtering: false,                               // disable filtering for column
     * inserting: false,                               // disable inserting for column
     * editing: false,                                 // disable editing for column
     * sorting: false,                                 // disable sorting for column

     * searchModeButtonTooltip: "Switch to searching", // tooltip of switching filtering/inserting button in inserting mode
     * insertModeButtonTooltip: "Switch to inserting", // tooltip of switching filtering/inserting button in filtering mode
     * editButtonTooltip: "Edit",                      // tooltip of edit item button
     * deleteButtonTooltip: "Delete",                  // tooltip of delete item button
     * searchButtonTooltip: "Search",                  // tooltip of search button
     * clearFilterButtonTooltip: "Clear filter",       // tooltip of clear filter button
     * insertButtonTooltip: "Insert",                  // tooltip of insert button
     * updateButtonTooltip: "Update",                  // tooltip of update item button
     * cancelEditButtonTooltip: "Cancel edit",         // tooltip of cancel editing button
     * }
     *
     * @return void
     */
    public function makeField_Control(): void
    {
        $this->Field[self::field_Type]             = "control";
        $this->Field[self::field_Width]            = "100";
        $this->Field[self::field_isSorting]        = false;
        $this->Field[self::field_isEditing]        = false;
        $this->Field[self::field_isInserting]      = false;
        $this->Field[self::field_isFiltering]      = false;

        // $this->Field['deleteButton']      = false;
    }

    /**
     * Make row number field
     *
     * @return void
     */
    public function makeField_RowNumber(): void
    {
        /**
         * To coordinate with the "jsgrid-ctrl.js" for add rows number,
         * This item must be saved with a name "Row"
         */
        $this->Field[self::field_Name]             = "Row";

        $this->setPropertiesCollection(self::ProCol_ShowOnly);
        $this->Field[self::field_Type]             = "number";
        $this->Field[self::field_isSorting]        = false;
        $this->Field[self::field_Width]            = "100";
        $this->Field[self::field_Align]            = "center";
    }

    /**
     * Make text field
     *
     * @return void
     */
    public function makeField_Text(): void
    {
        $this->Field[self::field_Type]             = "text";
        $this->Field[self::field_isAutosearch]     = true;
        $this->Field[self::field_isReadOnly]       = false;
        $this->Field[self::field_Sorter]           = "string";
    }

    /**
     * Make number field
     *
     * @return void
     */
    public function makeField_Number(): void
    {
        $this->Field[self::field_Type]             = "number";
        $this->Field[self::field_Align]            = "start";
        $this->Field[self::field_isAutosearch]     = true;
        $this->Field[self::field_isReadOnly]       = false;
        $this->Field[self::field_Sorter]           = "number";
    }

    /**
     * Make checkbox field
     *
     * @return void
     */
    public function makeField_Checkbox(): void
    {
        $this->Field[self::field_Type]             = "checkbox";
        $this->Field[self::field_isAutosearch]     = true; // triggers searching when the user clicks checkbox in filter
        $this->Field[self::field_isReadOnly]       = false;
        $this->Field[self::field_Width]            = "100";
        $this->Field[self::field_Sorter]           = "number"; // uses sorter for numbers
    }

    /**
     * Make textarea field
     *
     * @return void
     */
    public function makeField_Textarea(): void
    {
        $this->Field[self::field_Type]             = "textarea";
        $this->Field[self::field_isAutosearch]     = true; // triggers searching when the user presses `enter` key in the filter input
        $this->Field[self::field_isReadOnly]       = false;
        $this->Field[self::field_Sorter]           = "string";
    }

    /**
     * Make date field
     *
     * @param string $field_dateViewType Example:: dateViewType_DateString
     * @param string $customFormat Example:: "yy-mm-dd" -> output: 1984-02-25
     * @return void
     */
    public function makeField_Date(string $field_dateViewType = self::dateViewType_DateString, string $customFormat = null): void
    {
        if ($customFormat != null) {
            $field_dateViewType = sprintf("CustomFormat:%s", $customFormat);
        }

        $this->Field[self::field_Type]             = "date";
        $this->Field[self::field_dateViewType]     = $field_dateViewType;
        $this->Field[self::field_isAutosearch]     = true;
        $this->Field[self::field_isReadOnly]       = false;
    }

    /**
     * Make date range field
     *
     * @param string $field_dateViewType Example:: dateViewType_DateString
     * @param string $customFormat Example:: "yy-mm-dd" -> output: 1984-02-25
     * @return void
     */
    public function makeField_DateRange(string $field_dateViewType = self::dateViewType_DateString, string $customFormat = null): void
    {
        if ($customFormat != null) {
            $field_dateViewType = sprintf("CustomFormat:%s", $customFormat);
        }

        $this->Field[self::field_Type]             = "date_range";
        $this->Field[self::field_dateViewType]     = $field_dateViewType;
        $this->Field[self::field_isAutosearch]     = true;
        $this->Field[self::field_isReadOnly]       = false;
    }

    /**
     * Make number range field
     *
     * @return void
     */
    public function makeField_NumberRange(): void
    {
        $this->Field[self::field_Type]          = "number_range";
        $this->Field[self::field_css]           = "ltr";
        $this->Field[self::field_isAutosearch]  = true;
        $this->Field[self::field_isReadOnly]    = false;
    }

    /**
     * Make select field
     *
     * @param string $valueField        name of property of item to be used as value
     * @param string $textField         name of property of item to be used as displaying value
     * @param int|string $selectedIndex    index of selected item by default
     * @param string $valueType         the data type of the value: "number|string"
     * @param array $items              an array of items for select
     * @return void
     */
    public function makeField_Select(string $valueField = "id", string $textField = "Name", int|string $selectedIndex = -1, string $valueType = "number|string", array $items = []): void
    {
        $this->Field[self::field_Type]             = "select";
        $this->Field[self::field_isAutosearch]     = true; // triggers searching when the user changes the selected item in the filter
        $this->Field[self::field_isReadOnly]       = false;

        $this->Field["valueField"]                  = $valueField;   // name of property of item to be used as value
        $this->Field["textField"]                   = $textField;   // name of property of item to be used as displaying value
        $this->Field["selectedIndex"]               = $selectedIndex;   // index of selected item by default
        $this->Field["valueType"]                   = $valueType;   // "number|string", the data type of the value

        $this->Field["items"]                       = $items;   // // an array of items for select

        /**
         * If valueField is not defined, then the item index is used instead.
         * If textField is not defined, then item itself is used to display value.
         * For instance the simple select field config may look like:
         *
         * items: [ "", "United States", "Canada", "United Kingdom" ]
         *
         * or more complex with items as objects:
         *
         * {
         *     name: "Country",
         *     type: "select"
         *     items: [
         *                { Name: "", Id: 0 },
         *                { Name: "United States", Id: 1 },
         *                { Name: "Canada", Id: 2 },
         *                { Name: "United Kingdom", Id: 3 }
         *     ],
         *     valueField: "Id",
         *     textField: "Name"
         */
    }

    /*********************************************** Make Field END ***********************************************************/






    /*********************************************** Built-in Validators ***********************************************************/

    /***********************************************************************/
    //Consts
    /**
     * required - the field value is required
     * rangeLength - the length of the field value is limited by range (the range should be provided as an array in param field of validation config)
     * minLength - the minimum length of the field value is limited (the minimum value should be provided in param field of validation config)
     * maxLength - the maximum length of the field value is limited (the maximum value should be provided in param field of validation config)
     * pattern - the field value should match the defined pattern (the pattern should be provided as a string regexp in param field of validation config)
     * range - the value of the number field is limited by range (the range should be provided as an array in param field of validation config)
     * min - the minimum value of the number field is limited (the minimum should be provided in param field of validation config)
     * max - the maximum value of the number field is limited (the maximum should be provided in param field of validation config)
     */

    /**
     * Sample:
     *
     * jsGrid.validators.time = {
     *                    message: "Please enter a valid time, between 00:00 and 23:59",
     *                    validator: function(value, item) {
     *                          return /^([01]\d|2[0-3]|[0-9])(:[0-5]\d){1,2}$/.test(value);
     *                      }
     *                   }
     */

    /*
      * Sample2
                 {
                    name: "Name",
                    type: "text",
                    width: 150,
                    validate: "required"
                }
    */

    /*
        Sample3
                    name: "Age",
                    type: "number",
                    width: 50,
                    validate: {
                        validator: "range",
                        param: [18, 80]
                    }
    */
    /*
        Sample4
            {
                    name: "Country",
                    type: "select",
                    items: db.countries,
                    valueField: "Id",
                    textField: "Name",
                    validate: {
                        message: "Country should be specified",
                        validator: function(value) {
                            return value > 0;
                        }
                    }
                },
    */
    const
        validator_function = "function",
        validator_required = "required", validator_rangeLength = "rangeLength", validator_minLength = "minLength",
        validator_maxLength = "maxLength", validator_pattern = "pattern", validator_range = "range",
        validator_min = "min", validator_max = "max";

    //Consts END
    /***********************************************************************/


    /**
     * Add validation rules
     *
     * @param  string $validator :Select one items from "const validator_X" items. For example : validator_required
     * @param  string $param :If your validation requires a value, its value must be entered in this $param. For example : addValidate(validator_max, 20, 'The max alowed is: 20')
     * @param  string $message :Insert your error message here.
     * @return void
     */
    public function addValidate(string $validator, string $param = null, string $message = null): void
    {
        /**
         * samples:
         *
         * $fieldMaker = new jsGrid_FieldMaker("Name","Name");
         *
         * $fieldMaker->addValidate($fieldMaker::validator_required,null,"this item required!");
         *
         * //Sample of function $validator:
         * $fieldMaker->setValidate($fieldMaker::validator_function,"function(value) {return value > 20;}","The value entered must be greater than 20!");
         */

        $newValidator = [
            "validator"     =>  $validator,
            "param"         =>  $param,
            "message"       =>  $message,
        ];

        if ($validator == self::validator_function) {

            $newValidator["validator"] = jsGrid_Controller::markAsJavaCode($param);
            $newValidator["param"] = null;
        }

        if (is_null($this->Field[self::field_Validate]))
            $this->Field[self::field_Validate] = [];

        array_push($this->Field[self::field_Validate], array_filter($newValidator, fn ($value) => !is_null($value) && $value !== ''));
    }

    /*********************************************** Built-in Validators END ***********************************************************/
}
