class DomainPreparingReviewer {

    constructor(apiBaseUrl, classHandlerVar) {

        this.apiBaseUrl = apiBaseUrl;
        this.classHandlerVar = classHandlerVar;

        this.lastPassedIds = [];
        this.currentDomainId = -1;

        this.modalLoading = new Modal_loading();
        this.modalRealize = new ModalRealize('DomainPreparingReviewerModalRealize');
        this.modalConfirm = new ModalConfirm('DomainPreparingReviewerModalConfirm');

        this.domainNameDisplayView = func_getView("domainNameDisplay");
        this.descrView = func_getView('descr');

        this.submitSection = func_getView('submitSection');
        this.mobileCheckButtonsSection = func_getView('mobileCheckButtonsSection');
        this.desktopCheckButtonsSection = func_getView('desktopCheckButtonsSection');
        this.btnMobileSuccess = func_getView('btnMobileSucccess');
        this.btnMobileFailed = func_getView('btnMobileFailed');
        this.btnDesktopSuccess = func_getView('btnDesktopSucccess');
        this.btnDesktopFailed = func_getView('btnDesktopFailed');

        this.iframDomainCheckMobile = func_getView("iframDomainCheckMobile");
        this.iframDomainCheckDesktop = func_getView("iframDomainCheckDesktop");

        this.descrView.addEventListener('input', (event) => {

            this.descrView.innerHTML = this.descrView.value;
        });

        this.resetCheckParams();
        this.getDomainForReview();
    }

    /**
     * Reset check params
     */
    resetCheckParams() {

        this.descrView.value = "";

        /**
         * 0: default,
         * 1: mobile loaded
         * 2: desktop loaded
         * 3: both loaded
         */
        this.iframesLoadStatus = 0;

        /**
         * null: default,
         * true: loaded successfully
         * false: failed to load
         */
        this.mobileLoadRealut = null;

        /**
         * null: default,
         * true: loaded successfully
         * false: failed to load
         */
        this.desktopLoadRealut = null;

        func_displayView(this.submitSection.id, false);
        func_displayView(this.mobileCheckButtonsSection.id, false);
        func_displayView(this.desktopCheckButtonsSection.id, false);

        this.btnMobileSuccess.removeAttribute('disabled');
        this.btnMobileFailed.removeAttribute('disabled');
        this.btnDesktopSuccess.removeAttribute('disabled');
        this.btnDesktopFailed.removeAttribute('disabled');
    }

    /**
     * Informe view iframe loaded
     *
     * @param {string} type : mobile|desktop
     */
    iframeLoaded(type) {

        if (type == 'mobile')
            this.iframesLoadStatus += 1;
        if (type == 'desktop')
            this.iframesLoadStatus += 2;

        if (this.iframesLoadStatus == 3) {
            /* Both views loaded */

            func_displayView(this.submitSection.id, true);
            func_displayView(this.mobileCheckButtonsSection.id, true);
            func_displayView(this.desktopCheckButtonsSection.id, true);
        }
    }

    /**
     * Load result
     *
     * @param {string} type : mobile|desktop
     * @param {boolean} result
     */
    loadResult(type, result) {

        let btnSuccess, btnFailed;
        let failedDescr = null;

        if (type == 'mobile') {
            this.mobileLoadRealut = result;

            btnSuccess = this.btnMobileSuccess;
            btnFailed = this.btnMobileFailed;

            failedDescr = "Mobile load failed";

        } else if (type == 'desktop') {
            this.desktopLoadRealut = result;

            btnSuccess = this.btnDesktopSuccess;
            btnFailed = this.btnDesktopFailed;

            failedDescr = "Desktop load failed";
        }

        let descr = this.descrView.value;

        if (result) {
            btnSuccess.setAttribute('disabled', true);
            btnFailed.removeAttribute('disabled');
            descr = descr.replaceAll(failedDescr, "");
        } else {
            btnSuccess.removeAttribute('disabled');
            btnFailed.setAttribute('disabled', true);

            if (!descr.includes(failedDescr)) {
                if (!func_isEmpty(descr))
                    descr += "\n";

                descr += failedDescr;
            }
        }

        this.descrView.value = descr.trim();
    }

    /**
     * Refresh display iframes
     */
    refreshDisplays() {

        let descr = this.descrView.value;

        this.resetCheckParams();

        let desktopSrc = this.iframDomainCheckDesktop.src;
        let mobileSrc = this.iframDomainCheckMobile.src;

        this.iframDomainCheckDesktop.src = 'about:blank';
        this.iframDomainCheckMobile.src = 'about:blank';

        setTimeout(() => {
            this.iframDomainCheckDesktop.src = desktopSrc;
            this.iframDomainCheckMobile.src = mobileSrc;
        }, 300);

        this.descrView.value = descr;
    }

    /**
     * Get last passed domainId
     *
     * @returns integer
     */
    getLastPassedDomainId() {

        let lastPassedIdsCount = this.lastPassedIds.length;
        return lastPassedIdsCount > 0 ? this.lastPassedIds[lastPassedIdsCount - 1] : -1;
    }

    /**
     * Move backward step
     *
     * @param {integer} step
     * @returns
     */
    moveBackwardStep(step = 1) {

        let lastPassedIdsCount = this.lastPassedIds.length;

        if (lastPassedIdsCount < step) {
            this.lastPassedIds = [];
            window.location.reload();
            return;
        }

        let domainId = this.getLastPassedDomainId(lastPassedIdsCount - step);

        this.lastPassedIds.splice(lastPassedIdsCount - step, step);

        this.getDomainForReview(domainId);
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
        serverConnection.setSubUrl('domains/preparing_review/' + action);
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

    /**
     * Get domain for review
     */
    getDomainForReview(domainId = null) {

        this.resetCheckParams();

        var serverConnection = this.getServerConnection('get_domain_for_review');
        serverConnection.appendData("lastPassedId", this.getLastPassedDomainId());
        if (!func_isEmpty(domainId))
            serverConnection.appendData("forceId", domainId);


        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            var data = response.data;

            var domain = data.domain;

            if (func_isEmpty(domain)) {
                func_displayView("DomainCheckSection", false);
                func_displayView("NoDomainMsg", true);
            } else {
                func_displayView("DomainCheckSection", true);

                func_getView("remainingDomainsCount").innerHTML = data.remainingDomainsCount;

                let domainId = domain.id;
                let domainName = domain.name;
                let domainDescr = domain.descr;

                this.currentDomainId = domainId;

                this.descrView.value = func_replaceNull(domainDescr);
                this.domainNameDisplayView.innerHTML = domainName;

                let iframDomainCheckMobile = func_getView("iframDomainCheckMobile");
                let iframDomainCheckDesktop = func_getView("iframDomainCheckDesktop");

                iframDomainCheckMobile.src = "https://m." + domainName;
                iframDomainCheckDesktop.src = "https://" + domainName;
            }

            this.applyDefaultData(data);
            this.modalLoading.close();

        }.bind(this));

        serverConnection.connect();

    }

    /**
     * Submit review result
     */
    submit() {

        this.modalRealize.setHeader(trans('result.failed'));

        if (func_isEmpty(this.mobileLoadRealut)) {
            this.modalRealize.setBody(func_getView('UnknownMobileLoadResultMsg').value);
            this.modalRealize.create();
            return;
        }

        if (func_isEmpty(this.desktopLoadRealut)) {
            this.modalRealize.setBody(func_getView('UnknownDesktopLoadResultMsg').value);
            this.modalRealize.create();
            return;
        }

        /* Send review result */
        var serverConnection = this.getServerConnection('submit_review');
        serverConnection.appendData("domain", this.domainNameDisplayView.innerHTML);
        serverConnection.appendData("mobileLoadRealut", this.mobileLoadRealut);
        serverConnection.appendData("desktopLoadRealut", this.desktopLoadRealut);
        serverConnection.appendData("descr", this.descrView.value);

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            var data = response.data;

            this.lastPassedIds.push(this.currentDomainId);

            this.applyDefaultData(data);
            this.modalLoading.close();

            this.getDomainForReview();

        }.bind(this));

        serverConnection.connect();
    }
}
