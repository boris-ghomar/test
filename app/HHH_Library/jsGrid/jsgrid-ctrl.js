/************************** jsGrid Controller **************************************/

class jsGridController {

    constructor(ContainerId, apiBaseUrl) {

        this.ContainerId = ContainerId;
        this.apiBaseUrl = apiBaseUrl;

        this.setLocale("en"); /* default */

        this.setupStringFormat();

        this.modalRealize = new ModalRealize('modalRealize_Ctrl_' + this.ContainerId);
        this.modalConfirm = new ModalConfirm('modalConfirm_Ctrl_' + this.ContainerId);
    }

    /**
     * example:
     * this.ContainerId = "jsGrid";
     * this.locale = "fa";
     * return "{0}.locale('{1}')".format(this.ContainerId, this.locale);
     *
     * answer: jsGrid.locale('fa')
     */
    setupStringFormat() {

        /* First, checks if it isn't implemented yet. */
        if (!String.prototype.format) {
            String.prototype.format = function () {
                var args = arguments;
                return this.replace(/{(\d+)}/g, function (match, number) {
                    return typeof args[number] != 'undefined'
                        ? args[number]
                        : match
                        ;
                });
            };
        }

    }

    create() {
        var scriptElement = this.getjsGridScriptElement();

        scriptElement.innerHTML = "";
        scriptElement.innerHTML += this.getLocaleTag();
    }

    config(jsGridConfig_Json) {

        var data = JSON.parse(JSON.stringify(jsGridConfig_Json), function (key, value) {

            if (typeof value === "string" && value.startsWith("hhh_java(") && value.endsWith(")")) {

                value = value.substring(9, value.length - 1);
                return (0, eval)("(" + value + ")");
            }
            return value;
        });

        return this.setCustomLoadStrategy(data);
    }

    /**
     * Set custom load strategy
     *
     * This configuration prevents the jsGrid from
     * going to the first page after removing an item.
     *
     * @param {object} data
     * @returns {object} data
     */
    setCustomLoadStrategy(data) {

        var MyCustomDirectLoadStrategy = function (grid) {

            jsGrid.loadStrategies.PageLoadingStrategy.call(this, grid);
        };

        MyCustomDirectLoadStrategy.prototype = new jsGrid.loadStrategies.PageLoadingStrategy();

        MyCustomDirectLoadStrategy.prototype.finishDelete = function (deletedItem, deletedItemIndex) {
            var grid = this._grid;
            grid.option("data").splice(deletedItemIndex, 1);
            grid.refresh();
        };

        data.loadStrategy = function () {
            return new MyCustomDirectLoadStrategy(this);
        };

        return data;
    }

    /********************* Setter & Getter **********************/
    getjsGridScriptElement() {
        return document.getElementById(this.ContainerId + "Script");
    }


    setLocale(locale) {
        this.locale = locale;
    }
    getLocale() {
        return this.locale;
    }
    getLocaleTag() {
        return "{0}.locale('{1}')".format(this.ContainerId, this.getLocale());
    }


    /********************* Setter & Getter END **********************/

    /********************* Server Connection **********************/
    /**
     * This section uses the "ServerConnection" class to connect to the server
     * The library "ServerConnection" is my own.
     */

    appendItems(serverConnection, formData) {
        Object.keys(formData).forEach(key => {
            if (key != "" && key != undefined) {
                serverConnection.appendData(key, formData[key]);
            }
        });
    }

    appendHeaders(serverConnection) {
        serverConnection.appendHeader("locale", this.getLocale());
    }

    setupBaseConnection(subUrl, data, deferred) {

        var serverConnection = new ServerConnection(this.apiBaseUrl);
        serverConnection.setSubUrl(subUrl);
        this.appendHeaders(serverConnection);
        this.appendItems(serverConnection, data);

        serverConnection.setOnFailed(function (xhr, status, errorThrown) {
            var response = JSON.parse(xhr.responseText);

            this.modalRealize.setHeader(trans('server.ServerError') + ":\n " + xhr.status + " : " + xhr.statusText);
            if (typeof response.message === "string") {
                this.modalRealize.setBody(response.message);
            }
            else {
                var msg = "";
                try {
                    /* if type of message is array, try to convert that to string list */
                    response.message.forEach(element => {
                        msg += '* ' + element + "\n";
                    });
                } catch (error) {
                    msg = JSON.stringify(response.message, null, 2);
                }
                this.modalRealize.setBody(msg);
            }

            this.modalRealize.create();

            deferred.reject();
        }.bind(this));

        return serverConnection;
    }

    addRowNumber(serverResponse) {

        var dataArray = serverResponse.data;

        try {
            if (Array.isArray(dataArray)) {

                if (dataArray.length > 0) {

                    var currentPage = serverResponse.meta.current_page;
                    var perPage = serverResponse.meta.per_page;
                    var rowNumber = (currentPage - 1) * perPage;
                    dataArray.forEach(element => {
                        rowNumber++;
                        element.Row = rowNumber;
                    });
                }
            }

        } catch (error) {
            alert(error.message);
        }

        return dataArray;

    }

    loadDataService(subUrl, filter) {

        var deferred = $.Deferred();

        var serverConnection = this.setupBaseConnection(subUrl, filter, deferred);
        serverConnection.setMethod("POST");

        serverConnection.setOnSuccess(function (response) {
            var response = JSON.parse(response);

            var dataResult = {
                itemsCount: response.meta.total,
                data: this.addRowNumber(response),
            };
            deferred.resolve(dataResult);
        }.bind(this));


        serverConnection.connect();
        return deferred.promise();
    }

    insertItemService(subUrl, item) {

        var deferred = $.Deferred();

        var serverConnection = this.setupBaseConnection(subUrl, item, deferred);
        serverConnection.setMethod("POST");

        serverConnection.setOnSuccess(function (response) {
            var response = JSON.parse(response);

            var insertedItem = response.data;

            deferred.resolve(insertedItem);
        });


        serverConnection.connect();
        return deferred.promise();
    }

    updateItemService(subUrl, item) {

        var deferred = $.Deferred();

        var serverConnection = this.setupBaseConnection(subUrl, item, deferred);
        serverConnection.setMethod("POST");
        serverConnection.appendData("_method", "PUT");

        serverConnection.setOnSuccess(function (response) {
            var response = JSON.parse(response);

            var updatedItem = response.data;
            if ('Row' in item) {
                updatedItem.Row = item.Row;
            }
            deferred.resolve(updatedItem);
        });

        serverConnection.connect();
        return deferred.promise();
    }

    deleteItemService(subUrl, item) {

        var deferred = $.Deferred();

        var serverConnection = this.setupBaseConnection(subUrl, item, deferred);
        serverConnection.setMethod("POST");
        serverConnection.appendData("_method", "DELETE");

        serverConnection.setOnSuccess(function (response) {
            var response = JSON.parse(response);

            showSuccessToast(response.message);

            deferred.resolve();
        }.bind(this));

        serverConnection.connect();
        return deferred.promise();
    }

    exportExcelService() {

        showInfoToast(trans('export.reviewMessage'));

        /* Prevents consecutive clicks */
        var btnExportExcel = document.getElementById('btnExportExcel');
        btnExportExcel.disabled = true;
        setTimeout(function () {
            btnExportExcel.disabled = false;
        }, 5000);

        var reqUrl = subUrl + "/export_excel";

        var filter = $("#" + this.ContainerId).jsGrid("getFilter");
        var sorting = $("#" + this.ContainerId).jsGrid("getSorting");

        if (sorting.field !== undefined && sorting.order !== undefined) {

            filter.sortField = sorting.field;
            filter.sortOrder = sorting.order;
        }

        var deferred = $.Deferred();

        var serverConnection = this.setupBaseConnection(reqUrl, filter, deferred);
        serverConnection.setMethod("POST");

        serverConnection.setOnSuccess(function (response) {
            var response = JSON.parse(response);

            showSuccessToast(response.message);
            document.getElementById("iframeDownloadFile").src = response.data.downloadLink;

        }.bind(this));


        serverConnection.connect();
        return deferred.promise();
    }

    /**
     * Update customize settings of table
     *
     * @returns defer
     */
    updateCustomizeTableSettings() {

        var reqUrl = subUrl + "/update_customize_table_settings";

        var deferred = $.Deferred();

        /* Collect selected columns */
        let customizablePageSelectedColumnsViews = document.querySelectorAll('input[name="customizablePageColumns[]"]:checked');
        let customizablePageSelectedColumns = [];

        customizablePageSelectedColumnsViews.forEach(selectedColumnView => {

            customizablePageSelectedColumns.push(selectedColumnView.value);
        });

        let data = {
            customizablePageSelectedColumns: JSON.stringify(customizablePageSelectedColumns),
        };

        var serverConnection = this.setupBaseConnection(reqUrl, data, deferred);
        serverConnection.setMethod("POST");

        serverConnection.setOnSuccess(function (response) {
            var response = JSON.parse(response);

            window.location.reload();

        }.bind(this));

        serverConnection.connect();
        return deferred.promise();
    }

    /********************* Server Connection END **********************/

    /********************* Extra Functions **********************/
    setupNoDataScrollView() {

        var thisGrid = document.getElementById(this.ContainerId);

        var headerRow = thisGrid.getElementsByClassName('jsgrid-header-row')[0];

        var noDataRow = thisGrid.getElementsByClassName('jsgrid-nodata-row')[0];
        if (is_ElementExist(noDataRow)) {

            var cell = noDataRow.getElementsByClassName('jsgrid-cell')[0];
            cell.style.width = cell.offsetWidth + "px"; /* offsetWidth: current with in dispaly */
            cell.colSpan = 1;

            /**
             * Calculate the difference between a column and a header
             * and assign it to a new column so that the text "No data"
             * is always in the center of the display.
            */
            var newCell = document.createElement("td");
            newCell.className = 'jsgrid-cell';
            newCell.style.width = (headerRow.offsetWidth - cell.offsetWidth) + "px"; /* fill */
            noDataRow.appendChild(newCell);
        }
    }

    /********************* Extra Functions END **********************/
}

/************************** jsGrid Controller END **************************************/
