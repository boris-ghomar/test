{{-- Important files --}}

{{--
    Attention:
    These items may interfere with other jQueries, so they should be listed after all of them.

    such as:
        "back_office/assets/vendors/js/vendor.bundle.base.js"
--}}

{{--
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
--}}
{{-- jQuery Date Picker requireds --}}
<link rel="stylesheet" href="{{ url('assets/general/widgets/jsgrid/jquery/ui/1.12.1/themes/cupertino/jquery-ui.min.css') }}">
<link rel="stylesheet" href="{{ url('assets/general/widgets/jsgrid/jquery/ui/1.12.1/themes/cupertino/jquery-ui.min.css') }}">
<script src="{{ url('assets/general/widgets/jsgrid/jquery/jquery-3.5.1.min.js') }}"></script>
<script src="{{ url('assets/general/widgets/jsgrid/jquery/ui/1.12.1/jquery-ui.min.js') }}"></script>

{{-- jsGrid requireds --}}
<script src="{{ url('assets/general/widgets/jsgrid/jsgrid.min.js') }}"></script>
<script src="{{ url('assets/general/widgets/jsgrid/jsgrid-ctrl.min.js') }}"></script>
{{-- Important files END --}}

@if (!App::isLocale('en'))
    <script src="{{ url(sprintf('assets/general/widgets/jsgrid/i18n/jsgrid-%s.js', App::getlocale())) }}"></script>
@endif



<script id="{{ $jsGrid_VariableNames['ContainerId'] }}Script"></script>

<script>
    var apiBaseUrl = "{{ $jsGrid_VariableNames['apiBaseUrl'] }}";
    var subUrl = "{{ $jsGrid_VariableNames['apiSubUrl'] }}";


    var jsGridCtrl = new jsGridController("{{ $jsGrid_VariableNames['ContainerId'] }}", apiBaseUrl);

    jsGridCtrl.setLocale("{{ App::getlocale() }}");
    jsGridCtrl.create();

    var modalRealize = new ModalRealize('modalRealize_' + jsGridCtrl.ContainerId);
    var modalConfirm = new ModalConfirm('modalConfirm_' + jsGridCtrl.ContainerId);
</script>

<script>

    {{-- jsGrid View --}}

    $(function() {


        {{-- config data --}}
        $("#{{ $jsGrid_VariableNames['ContainerId'] }}").jsGrid(jsGridCtrl.config( @json($jsGridConfig, JSON_PRETTY_PRINT)));
        {{-- config data END --}}

        {{-- callback functions --}}
        $("#{{ $jsGrid_VariableNames['ContainerId'] }}").jsGrid({


            onDataLoading: function(args) {},

            onDataLoaded: function(args) {

                jsGridCtrl.setupNoDataScrollView();
            },

            onOptionChanged: function(args) {

                jsGridCtrl.setupNoDataScrollView();
            },


            {{-- @override --}}
            invalidNotify: function(args) {

                var messages = $.map(args.errors, function(error) {
                    return error.message || null;
                });

                modalRealize.setHeader([this.invalidMessage]);
                modalRealize.setBody([].concat(messages).join("\n"));
                modalRealize.create();
            },

            {{-- @override --}}
            deleteItem: function(item) {
                var $row = this.rowByItem(item);

                if (!$row.length) return;

                if (this.confirmDeleting) {

                    if (typeof this.deleteConfirm === "function" ||
                        (typeof this.deleteConfirm === "string" && this.deleteConfirm.startsWith("function("))
                    ) {
                        var deleteConfirmFunc = eval(this.deleteConfirm);
                        modalConfirm.setBody(deleteConfirmFunc(item));
                    } else {
                        modalConfirm.setBody(this.deleteConfirm);
                    }

                    modalConfirm.setHeader(trans('alert.Delete'));
                    modalConfirm.setOnYesPressed(function() {
                        try {
                            return this._deleteRow($row);
                        } catch (error) {
                            alert(error.message)
                        }

                    }.bind(this));
                    modalConfirm.create();

                } else {
                    return this._deleteRow($row);
                }

                return;
            },


        });
        {{-- callback functions END --}}

    });

    {{-- jsGrid View END --}}




    {{-- jsGrid db data --}}


        (function($) {

            (function() {

                var {{ $jsGrid_VariableNames['dbName'] }} = {


                    loadData: function(filter) {
                        var reqUrl = (filter.pageIndex > 0) ? subUrl + "?page=" + filter.pageIndex : subUrl;
                        return jsGridCtrl.loadDataService(reqUrl, filter);
                    },

                    insertItem: function(item) {
                        var reqUrl = subUrl + "/insert";
                        return jsGridCtrl.insertItemService(reqUrl, item);
                    },

                    updateItem: function(item) {
                        var reqUrl = subUrl + "/update";
                        return jsGridCtrl.updateItemService(reqUrl, item);
                    },

                    deleteItem: function(item) {
                        var reqUrl = subUrl + "/delete";
                        return jsGridCtrl.deleteItemService(reqUrl, item);
                    },

                };

                <?php
                echo sprintf("window.%s = %s;\n\n", $jsGrid_VariableNames['dbName'], $jsGrid_VariableNames['dbName']);

                foreach ($data as $key => $value) {
                    echo sprintf("%s.%s = %s;\n\n", $jsGrid_VariableNames['dbName'], $key, json_encode($value, JSON_PRETTY_PRINT));
                }
                ?>

            }());

        })(jQuery);

    {{-- jsGrid db data END --}}
</script>
