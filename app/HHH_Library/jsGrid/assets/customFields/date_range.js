
/************************** DateRangeField **************************************/
(function (jsGrid, $, undefined) {

    var Field = jsGrid.Field;

    function DateRangeField(config) {
        Field.call(this, config);
    }

    DateRangeField.prototype = new Field({

        autosearch: true,
        readOnly: false,

        filterTemplate: function () {
            if (!this.filtering)
                return "";

            var grid = this._grid,
                $result = this.filterControl = this._createDateRangeBox();

            if (this.autosearch) {
                $result.on("keypress", function (e) {
                    if (e.which === 13) {
                        grid.search();
                        e.preventDefault();
                    }
                });
            }

            return $result;
        },

        insertTemplate: function () {
            if (!this.inserting)
                return "";

            return this.insertControl = this._createTextBox();
        },

        editTemplate: function (value) {
            if (!this.editing)
                return this.itemTemplate.apply(this, arguments);

            var $result = this.editControl = this._createTextBox();
            $result.val(value);
            return $result;
        },

        filterValue: function () {
            var fromDate = this.filterControl.children().val();
            var toDate = this.filterControl.children().next().val();

            var DateRange = {
                fromDate: fromDate,
                toDate: toDate,
            };

            return JSON.stringify(DateRange);
        },

        insertValue: function () {
            return this.insertControl.val();
        },

        editValue: function () {
            return this.editControl.val();
        },

        _createTextBox: function () {
            return $("<input>").attr("type", "text")
                .prop("readonly", !!this.readOnly)
                .on('input', function (event) {
                    element = event.target;
                    value = element.value;
                    element.value = value.replace(/[^0-9/\-: ]/g, '');
                });
        },

        _createDateRangeBox: function () {

            var $result = $("<div>")
                .prop("readonly", !!this.readOnly)
                ;

            var $fromDate = $("<input>").attr("type", "text")
                .prop("readonly", !!this.readOnly)
                .prop("id", "from_date")
                .prop("placeholder", trans('date.fromDate'))
                .on('input', function (event) {
                    element = event.target;
                    value = element.value;
                    element.value = value.replace(/[^0-9/\-: ]/g, '');
                })
                .appendTo($result);
            ;

            var $toDate = $("<input>").attr("type", "text")
                .prop("readonly", !!this.readOnly)
                .prop("id", "to_date")
                .prop("placeholder", trans('date.toDate'))
                .on('input', function (event) {
                    element = event.target;
                    value = element.value;
                    element.value = value.replace(/[^0-9/\-: ]/g, '');
                })
                .appendTo($result);
            ;

            $result.prop("disabled", !!this.readOnly);

            return $result;
        }
    });

    jsGrid.fields.date_range = jsGrid.DateRangeField = DateRangeField;

}(jsGrid, jQuery));
/************************** DateRangeField END **************************************/
