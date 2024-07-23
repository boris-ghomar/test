
/************************** DateField **************************************/
(function (jsGrid, $, undefined) {

    var DateField = function (config) {
        this.dateViewType = config.dateViewType;
        jsGrid.Field.call(this, config);
    };

    DateField.prototype = new jsGrid.Field({


        sorter: function (date1, date2) {
            return new Date(date1) - new Date(date2);
        },

        itemTemplate: function (value) {
            return this._getDateView(value);
        },

        filterTemplate: function () {
            if (!this.filtering)
                return "";

            var grid = this._grid,
                $result = this.filterControl = this.insertTemplate();

            if (this.autosearch) {
                $result.on("change", function (e) {
                    grid.search();
                    e.preventDefault();
                });
                $result.on("keypress", function(e) {
                    if(e.which === 13) {
                        grid.search();
                        e.preventDefault();
                    }
                });
            }

            return $result;
        },

        insertTemplate: function (value) {
            if (!this.inserting || this.readOnly)
                return "";

            return this._insertPicker = $("<input>").datepicker({ defaultDate: new Date(), dateFormat: 'yy-mm-dd' });
        },

        editTemplate: function (value) {
            if (!this.editing || this.readOnly)
                return this.itemTemplate.apply(this, arguments);

            return this._editPicker = $("<input>").datepicker({ dateFormat: 'yy-mm-dd' }).datepicker("setDate", new Date(value));
        },

        filterValue: function() {
            return this.filterControl.val();
        },

        insertValue: function () {
            try {
                return this._insertPicker.datepicker("getDate").toDateString();
            } catch (error) {
                return "";
            }
        },

        editValue: function () {
            try {
                return this._editPicker.datepicker("getDate").toDateString();
            } catch (error) {
                return "";
            }
        },


        _getDateView: function (value) {

            try {
                var dateViewType = this.dateViewType;
                var CustomKeyword = "CustomFormat:";

                if (dateViewType.startsWith(CustomKeyword)) {

                    var dateFormat = dateViewType.replace(CustomKeyword, '');
                    return $.datepicker.formatDate(dateFormat, new Date(value));

                } else {
                    var dateFunc = 'new Date("' + value + '").' + dateViewType + '();';
                    return eval(dateFunc);
                }
            } catch (error) {
                // alert(error.message);
                return new Date(value).toDateString();
            }

        },
    });

    jsGrid.fields.date = jsGrid.DateField = DateField;

}(jsGrid, jQuery));
/************************** DateField END **************************************/
