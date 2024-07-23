
/************************** NumberRangeField **************************************/
(function (jsGrid, $, undefined) {

    var Field = jsGrid.Field;

    function NumberRangeField(config) {
        Field.call(this, config);
    }

    NumberRangeField.prototype = new Field({

        autosearch: true,
        readOnly: false,

        filterTemplate: function () {
            if (!this.filtering)
                return "";

            var grid = this._grid,
                $result = this.filterControl = this._createNumberRangeBox();

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

            /* Remove the thousands separator when editing */
            $result.val(String(value).replaceAll(',', ''));
            return $result;
        },

        filterValue: function () {
            var fromNumber = this.filterControl.children().val();
            var toNumber = this.filterControl.children().next().val();

            var numberRange = {
                fromNumber: fromNumber,
                toNumber: toNumber,
            };

            return JSON.stringify(numberRange);
        },

        insertValue: function () {
            return this.insertControl.val();
        },

        editValue: function () {
            return this.editControl.val();
        },

        _createTextBox: function () {
            return $("<input>").attr("type", "number")
                .prop("readonly", !!this.readOnly)
                .on('input', function (event) {
                    element = event.target;
                    value = element.value;
                });
        },

        _createNumberRangeBox: function () {

            var $result = $("<div>")
                .prop("readonly", !!this.readOnly)
                ;

            var $fromNumber = $("<input>").attr("type", "number")
                .prop("readonly", !!this.readOnly)
                .prop("id", "from_number")
                .prop("placeholder", trans('number.fromNumber'))
                .on('input', function (event) {
                    element = event.target;
                    value = element.value;
                })
                .appendTo($result);
            ;

            var $toNumber = $("<input>").attr("type", "number")
                .prop("readonly", !!this.readOnly)
                .prop("id", "to_number")
                .prop("placeholder", trans('number.toNumber'))
                .on('input', function (event) {
                    element = event.target;
                    value = element.value;
                })
                .appendTo($result);
            ;

            $result.prop("disabled", !!this.readOnly);

            return $result;
        }
    });

    jsGrid.fields.number_range = jsGrid.NumberRangeField = NumberRangeField;

}(jsGrid, jQuery));
/************************** NumberRangeField END **************************************/
