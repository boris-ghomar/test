class DomainsImporter {

    constructor(apiBaseUrl, payload, dataDispalyTableViewId, classHandlerVar, importViewId) {

        this.apiBaseUrl = apiBaseUrl;
        this.payload = payload;
        this.classHandlerVar = classHandlerVar;

        this.modalLoading = new Modal_loading();
        this.modalRealize = new ModalRealize('ImportDomainsModalRealize');
        this.modalConfirm = new ModalConfirm('ImportDomainsModalConfirm');

        this.importView = this.getView(importViewId);
        this.dataDispalyTableView = this.getView(dataDispalyTableViewId);

        var tbodyView = document.createElement("tbody");
        tbodyView.id = "DataTableRows";
        this.dataDispalyTableView.appendChild(tbodyView);

        this.mainCanvas = tbodyView;

        this.progressbarData = null;
        this.setProgressbar(-1);

        this.importBtn = this.getView("importBtn");

        this.tableDataRowTemplate = this.getView("TableDataRowTemplate");

        this.clearCanvas(this.mainCanvas);
        this.setupImportView();
    }

    /**
     * Get view template HTML
     *
     * @param {string} viewId
     * @param {boolean} showError
     * @returns {viewObj|null}
     */
    getView(viewId, showError = false) {

        try {
            var view = document.getElementById(viewId);
            return is_ElementExist(view) ? view : null;
        } catch (error) {
            if (showError) {

                alert('No view found with this ID: ' + viewId);
                this.log(error);
            }
        }
        return null;
    }

    /**
     * Clear canvas of view
     *
     * @param {object} viewObj
     */
    clearCanvas(viewObj) {

        viewObj.innerHTML = "";
    }

    /**
     * Check if value is empty
     *
     * @param {string|int} value
     * @returns
     */
    isEmpty(value) {

        if (value === undefined) return true;
        if (value === null) return true;
        if (value === "") return true;

        return false;
    }

    /**
     * Display view
     *
     * @param {string} id
     * @param {boolean} show
     */
    displayView(id, show = true) {

        var view = this.getView(id);

        if (is_ElementExist(view)) {

            var hideClass = 'd-none';
            var isHidden = func_hasClass(view, hideClass);

            if (show && isHidden)
                toggleElementClass(id, hideClass);
            else if (!show && !isHidden)
                toggleElementClass(id, hideClass);
        }
    }

    /**
     * Replace Null with empty text
     *
     * @param {string} text
     * @returns {string}
     */
    replaceNull(text) {

        return text == null ? "" : text;
    }

    /**
     * Convert value to boolean cast
     *
     * @param {string|boolean} value
     * @returns {boolean}
     */
    booleanValue(value) {

        value = String(value).toLowerCase();

        if (value == "true") return true;
        if (value == "1") return true;

        return false;
    }

    /**
     * Remove view by id
     *
     * @param {string} viewId
     */
    removeView(viewId) {

        var view = document.getElementById(viewId);

        while (view != null) {
            view.parentNode.removeChild(view);
            view = document.getElementById(viewId);
        }
    }

    /**
     * Set progressbar
     *
     * @param {int} percent : set negative number to hide progress bar and setup initial data
     */
    setProgressbar(percent) {

        var progressbarContainerView = this.getView('progressbarContainer');
        var progressbarView = this.getView('progressbar');

        if (percent < 0) {
            this.displayView(progressbarContainerView.id, false);
            percent = 0;
            this.progressbarData = null;
            this.setupProgressbarData();
        } else {
            this.displayView(progressbarContainerView.id, true);
        }
        if (percent > 100)
            percent = 100;

        percent = percent.toFixed(1);

        progressbarView.style.width = percent + '%';
        progressbarView.setAttribute('aria-valuenow', percent);
        progressbarView.innerHTML = percent + '%';
    }

    /**
     * Setup progress bar data
     *
     * @param {int|null} min
     * @param {int|null} max
     * @param {int|null} currentPos
     * @param {int|null} stepSize
     */
    setupProgressbarData(min = null, max = null, stepSize = null) {

        var progressbarData = this.progressbarData;

        if (progressbarData == null) {
            /* Initial settings */

            progressbarData = {
                currentPos: -1,
                stepSize: stepSize == null ? 0 : stepSize,
                min: min == null ? -1 : min,
                max: max == null ? 100 : max,
            };
        } else {

            if (stepSize != null)
                progressbarData.stepSize = stepSize;

            if (min != null)
                progressbarData.min = min;

            if (max != null)
                progressbarData.max = max;
        }
        this.progressbarData = progressbarData;
    }

    /**
     * Move progress bar froward
     */
    progressbarMoveForward() {

        var progressbarData = this.progressbarData;

        if (progressbarData.currentPos < progressbarData.min)
            progressbarData.currentPos = progressbarData.min;

        progressbarData.currentPos += progressbarData.stepSize;

        if (progressbarData.currentPos > progressbarData.max)
            progressbarData.currentPos = progressbarData.max;

        this.setProgressbar(progressbarData.currentPos);

    }

    /**
     * Get a temporary ID
     *
     * @param {string} prefix
     */
    getTemporaryID(prefix = null) {

        var rnd = Math.floor(Math.random() * 100) + 1;
        var id = Date.now() + "" + rnd;

        if (prefix != null) {

            prefix = prefix.trim();
            id = prefix + id;
        }

        return is_ElementExist(this.getView(id)) ? this.getTemporaryID(prefix) : id;
    }

    /**
     * Setup domains import view table
     *
     */
    setupImportView() {

        var importView = this.importView;

        importView.addEventListener("input", (event) => {

            this.clearCanvas(this.mainCanvas);

            var data = importView.value.trim();

            var rows = data.split("\n");

            var rowIndex = 0;

            rows.forEach(row => {

                if (!this.isEmpty(row)) {

                    var fields = row.split("\t");

                    var rowData = {
                        index: rowIndex + 1,
                    };

                    var tableFieldNames = JSON.parse(document.getElementById('TableFieldNames').value);

                    var fieldIndex = 0;
                    tableFieldNames.forEach(fieldName => {

                        var fieldValue = fields[fieldIndex];

                        if (this.isEmpty(fieldValue))
                            fieldValue = "";

                        rowData[fieldName] = fieldValue;
                        fieldIndex++;

                    });

                    this.addTemplateToView(this.mainCanvas, this.tableDataRowTemplate, 'DataRow_' + rowIndex, rowData);

                    rowIndex++;
                }
            });


        });
    }

    /**
     * Add template to view
     *
     * @param {viewObject} parentView
     * @param {viewObject} template
     * @param {string} id
     * @param {object} data
     */
    addTemplateToView(parentView, template, id, data) {

        var template_innerHTML = this.modifyTempalte(template, id, data);

        if (id == null) {

            parentView.innerHTML += template_innerHTML;
        }
        else {

            var templateTagName = template.tagName.toLowerCase();

            var newView = document.createElement(templateTagName);
            newView.id = id;
            newView.innerHTML = template_innerHTML;
            parentView.appendChild(newView);
        }
    }

    /**
     * Modify tempalte to insert in page
     *
     * @param {view object} template
     * @param {string} id
     * @param {object} data
     * @returns {string}
     */
    modifyTempalte(template, id, data) {

        if (id == null)
            id = this.getTemporaryID();

        var template_innerHTML = template.innerHTML;

        /* TableDataRowTemplate */
        if (template.id == this.tableDataRowTemplate.id) {

            for (const [fieldKey, fieldValue] of Object.entries(data)) {
                template_innerHTML = template_innerHTML.replaceAll(fieldKey + "_value", fieldValue);
            }
        }

        return template_innerHTML;
    }

    /**
     * Get server connection handler base on action
     *
     * @param {string} action
     * @param {boolean} displayWaiting
     * @returns serverConnection handler
     */
    getServerConnection(action, onFailedCallbackFunc = null, displayWaiting = true) {

        if (displayWaiting)
            this.modalLoading.show();

        var serverConnection = new ServerConnection(this.apiBaseUrl);
        serverConnection.setSubUrl('domains/import_domains/' + action);
        serverConnection.setMethod("POST");
        serverConnection.appendHeader("locale", locale);
        serverConnection.appendData("payload", this.payload);

        serverConnection.setOnFailed(function (xhr, status, errorThrown) {
            var response = JSON.parse(xhr.responseText);

            this.modalRealize.setHeader(trans('result.failed'));
            if (typeof response.message === "string") {
                this.modalRealize.setBody(response.message);
            } else {
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

            this.modalLoading.close();
            this.modalRealize.create();

            if (onFailedCallbackFunc != null)
                onFailedCallbackFunc(response);

        }.bind(this));

        return serverConnection;

    }

    /**
     * Apply default page data
     *
     * @param {object} data
     * @returns
     */
    applyDefaultData(data) {

        if (data == null)
            return;

        /* Update  CSRF token */
        var csrfToken = data.csrfToken;
        if (!this.isEmpty(csrfToken)) {

            var csrfTokenElement = document.getElementsByName('csrf-token');
            if (csrfTokenElement.length > 0) {

                csrfTokenElement = csrfTokenElement[0];

                csrfTokenElement.content = csrfToken;
            }
        }

        /* Debug mode */
        if (!this.isEmpty(data.debugMode))
            this.debugMode = this.booleanValue(data.debugMode);
    }

    /**
     * Import data to database
     *
     * @param {boolean} confirmed
     */
    importData(isConfirmed = false) {

        if (this.isEmpty(this.importView.value.trim())) {
            this.modalRealize.setHeader(trans('result.failed'));
            this.modalRealize.setBody(document.getElementById('NoDataMsg').value);
            this.modalRealize.create();
            return;
        }

        if (isConfirmed) {

            /* The maximum number of data records allowed to be sent per request */
            const maxAllowedSendData = 20;

            this.displayView(this.importBtn.id, false);

            if (this.lastSentRecordIndex < 0) {

                let recordsCount = this.mainCanvas.getElementsByTagName('tr').length;
                let stepSize = 100 / recordsCount;
                this.setupProgressbarData(0, 100, stepSize);
            }

            var isListFinished = false;
            var data = {};

            let startIndex = this.lastSentRecordIndex + 1;
            let endIndex = this.lastSentRecordIndex + 1 + maxAllowedSendData;

            for (let rowIndex = startIndex; rowIndex < endIndex; rowIndex++) {

                const dataRowId = "DataRow_" + rowIndex;
                const row = document.getElementById(dataRowId);
                if (is_ElementExist(row)) {

                    var rowChilds = row.getElementsByTagName('td');

                    let rowData = {
                        rowIndex: rowIndex,
                    };
                    for (let i = 0; i < rowChilds.length; i++) {
                        const rowChild = rowChilds[i];

                        const fieldKey = rowChild.getAttribute("name");

                        if (!this.isEmpty(fieldKey)) {

                            rowData[fieldKey] = rowChild.innerHTML;

                        }
                    }
                    data[rowIndex] = rowData;
                    this.lastSentRecordIndex = rowIndex;

                } else {
                    isListFinished = true;
                }
            }

            if (Object.keys(data).length < 1) {
                this.setProgressbar(-1);
                this.displayView(this.importBtn.id, true);
                this.modalLoading.close();
                return;
            }

            /* Send data */
            let onFailedCallbackFunc = () => {

                this.setProgressbar(-1);
                this.displayView(this.importBtn.id, true);
                this.modalLoading.close();
            };

            var serverConnection = this.getServerConnection('import', onFailedCallbackFunc);
            serverConnection.appendData('domain_category_id', this.getView('domain_category_id').value);
            serverConnection.appendData('domain_holder_account_id', this.getView('domain_holder_account_id').value);
            serverConnection.appendData('Overwrite', this.getView('Overwrite').value);
            serverConnection.appendData('domains', JSON.stringify(data));

            serverConnection.setOnSuccess(function (responseJson) {
                var response = JSON.parse(responseJson);

                var data = response.data;
                var importResults = data.importResults;

                importResults.forEach(item => {

                    let rowIndex = item.rowIndex;
                    let result = item.result;
                    let status = result.status;
                    let message = result.message;

                    let msgColorClass = "text-success";

                    if (status == "Failed") {

                        msgColorClass = "text-danger";
                        message = "";
                        result.message.forEach(element => {
                            message += '* ' + element + "<br>";
                        });
                    } else if (status == "Ignored") {
                        msgColorClass = "text-warning";
                    }

                    let resultMsg = "status: " + status;
                    resultMsg += "<br>" + message;

                    let resultView = this.getView("DataRowReslut_" + (rowIndex + 1));
                    resultView.className = msgColorClass;
                    resultView.innerHTML = resultMsg;

                    this.progressbarMoveForward();
                });

                this.applyDefaultData(data);

                if (isListFinished) {
                    this.setProgressbar(-1);
                    this.displayView(this.importBtn.id, true);
                    this.modalLoading.close();
                } else {
                    setTimeout(() => {
                        this.importData(true);
                    }, 1100);
                }

            }.bind(this));

            serverConnection.connect();

        } else {
            this.lastSentRecordIndex = -1;

            var modalConfirm = this.modalConfirm;
            var confirmMessage = document.getElementById('ConfirmImportMsg').value;
            modalConfirm.setHeader(document.getElementById('ConfirmImportTitle').value);
            modalConfirm.setBody(confirmMessage);
            modalConfirm.create();

            modalConfirm.setOnYesPressed(() => {
                this.importData(true);
            });
        }
    }

}
