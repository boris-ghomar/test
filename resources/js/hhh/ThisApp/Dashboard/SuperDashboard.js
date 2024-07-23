class SuperDashboard {

    constructor(apiBaseUrl) {

        this.apiBaseUrl = apiBaseUrl;

        this.modalLoading = new Modal_loading();
        this.modalRealize = new ModalRealize('DashboardModalRealize');
        this.modalConfirm = new ModalConfirm('DashboardModalConfirm');
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
        serverConnection.setSubUrl('dashboard/' + action);
        serverConnection.setMethod("POST");
        serverConnection.appendHeader("locale", locale);

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
        if (!func_isEmpty(csrfToken)) {

            var csrfTokenElement = document.getElementsByName('csrf-token');
            if (csrfTokenElement.length > 0) {

                csrfTokenElement = csrfTokenElement[0];

                csrfTokenElement.content = csrfToken;
            }
        }

        /* Debug mode */
        if (!func_isEmpty(data.debugMode))
            this.debugMode = func_booleanValue(data.debugMode);
    }

}
