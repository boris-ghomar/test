class DomainController extends SuperDashboard {

    constructor(apiBaseUrl) {
        super(apiBaseUrl);

        this.bcPermenantDomain = func_getView('domain_bcPermenantDomainView').value;
        this.bcUnblockedDomain = func_getView('domain_bcUnblockedDomainView').value;

        this.domainIdView = func_getView('domainIdView');

        this.setSiteUrls();
    }

    /**
     * Check if device is vertical
     *
     * @returns bool
     */
    isDeviceVertical() {
        return screen.height > screen.width ? true : false;
    }

    /**
     * Convert domain to URL and set views
     */
    setSiteUrls() {

        let bcPermenantUrlView = func_getView('domain_bcPermenantUrlView');
        let bcUnblockedUrlView = func_getView('domain_bcUnblockedUrlView');

        let bcPermenantUrl = "https://www." + this.bcPermenantDomain + "/fa";
        let bcUnblockedUrl = "https://www." + this.bcUnblockedDomain + "/fa";

        if (this.isDeviceVertical()) {
            bcPermenantUrl = "https://m." + this.bcPermenantDomain + "/fa";
            bcUnblockedUrl = "https://m." + this.bcUnblockedDomain + "/fa";
        }

        bcPermenantUrlView.href = bcPermenantUrl;
        bcPermenantUrlView.innerHTML = bcPermenantUrl;

        bcUnblockedUrlView.href = bcUnblockedUrl;
        bcUnblockedUrlView.innerHTML = bcUnblockedUrl;

    }

    /**
     * Copy site URL to clipboard
     */
    copySiteUrlToClipboard(urlContainerView) {

        func_copyToClipboard(func_getView(urlContainerView).href);
        showSuccessToast(func_getView('DomainCopiedMsg').value);
    }

    /**
     * Report domain issue by client
     *
     */
    reportDomainIssue() {

        let modalConfirm = this.modalConfirm;
        modalConfirm.setHeader(trans('alert.Confirm'));
        modalConfirm.setBody(func_getView('DomainReportConfirmMsg').value);
        modalConfirm.create();

        modalConfirm.setOnYesPressed(() => {

            var serverConnection = this.getServerConnection('domain/report_domain_issue');
            serverConnection.appendData("domainId", this.domainIdView.value);

            serverConnection.setOnSuccess(function (responseJson) {
                var response = JSON.parse(responseJson);

                var data = response.data;
                var message = response.message;

                let btnDomainReport = func_getView('btnDomainReport');
                func_displayView(btnDomainReport.id, false);

                let domainMsgView = func_getView('domainMsgView');
                domainMsgView.innerHTML = message;

                let domainId = data.domainId;

                this.bcUnblockedDomain = data.bcUnblockedDomain;

                this.domainIdView.value = domainId;

                this.setSiteUrls();
                this.applyDefaultData(data);
                this.modalLoading.close();

            }.bind(this));

            serverConnection.connect();
        });
    }
}
