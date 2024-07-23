class ClientProfileController {

    constructor(apiBaseUrl) {

        this.apiBaseUrl = apiBaseUrl;
        this.debugMode = false;

        this.modalLoading = new Modal_loading();
        this.modalRealize = new ModalRealize('ProfileModalRealize');

        this.provincesCitiesCollection = [];
        this.provinceSelectView = func_getView('province_internal');
        this.citySelectView = func_getView('city_internal');

        this.selectedCity = func_getView(this.citySelectView.id + '_selectedItem').value;
        this.provinceSelectView.addEventListener('change', (event) => { this.provinceChanged() });

        this.showIncompleteTab();
        this.provinceChanged();
    }

    /**
     * Show incomplete tab
     */
    showIncompleteTab() {

        /*
        * When one of the tabs is submitted and an error occurred while saving,
        * do not switch the tab
        */
        var tabErrorView = document.querySelector('[id$="_tab_errors"]');
        if (!func_isEmpty(tabErrorView))
            return;

        /* Register items by priority */
        let needCompleteTabs = [
            'further_information',
            'change_email',
        ];

        let currentUrl = window.location.href;
        let urlSplit = currentUrl.split('#');

        needCompleteTabs.every(tabName => {

            let tabViewId = tabName + '-tab';
            let incompleteViewId = tabName + '-incomplete';

            if (is_ElementExistById(incompleteViewId)) {

                let tabAnchorLink = '#' + tabName;

                sessionStorage.setItem('activeTab', tabAnchorLink);
                func_getView(tabViewId).click();
                window.location.href = urlSplit[0] + tabAnchorLink;

                if (tabName == "further_information") {

                    if (!is_ElementExistById(tabName + '_tab_errors')) {

                        let formView = func_getView(tabName + '_form');
                        formView.submit();
                    }
                }

                return false;
            }
            return true;
        });
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
        serverConnection.setSubUrl('profile/' + action);
        serverConnection.setMethod("POST");
        serverConnection.appendHeader("locale", locale);
        serverConnection.appendData("_apiAction", action);

        serverConnection.setOnFailed(function (xhr, status, errorThrown) {
            var response = JSON.parse(xhr.responseText);

            this.modalRealize.setHeader(trans('result.failed'));
            if (typeof response.message === "string") {
                this.modalRealize.setBody(response.message);
            } else {
                var errorMessage = "";
                try {
                    /* if type of message is array, try to convert that to string list */
                    response.message.forEach(element => {
                        errorMessage += '* ' + element + "\n";
                    });
                } catch (error) {
                    errorMessage = JSON.stringify(response.message, null, 2);
                }
                this.modalRealize.setBody(errorMessage);
            }

            if (errorMessage == "exception.CSRF token mismatch.")
                window.location.reload();

            if (response.data != null) {

                if (response.data.refreshPage === true)
                    window.location.reload();
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
     * Province dropdown changed
     */
    provinceChanged() {

        let provincesCitiesCollection = this.provincesCitiesCollection;

        let selectedProvince = this.provinceSelectView.value;
        let cities = provincesCitiesCollection[selectedProvince];

        if (func_isEmpty(cities)) {

            var serverConnection = this.getServerConnection('get_cities');
            serverConnection.appendData("province", selectedProvince);

            serverConnection.setOnSuccess(function (responseJson) {
                var response = JSON.parse(responseJson);

                var data = response.data;

                let cities = data.cities;
                this.provincesCitiesCollection[selectedProvince] = cities;
                this.modifyCitiesDropdown(cities);

                this.applyDefaultData(data);
                this.modalLoading.close();

            }.bind(this));

            serverConnection.connect();
        } else {
            this.modifyCitiesDropdown(cities);
        }

    }

    /**
     * Change cities dropdown options
     *
     * @param {array} cities
     */
    modifyCitiesDropdown(cities) {

        let citySelectView = this.citySelectView;
        citySelectView.innerHTML = "";

        var displayCitiesView = false;

        func_forEach(cities, (text, key) => {

            let option = document.createElement('option');
            option.value = key;
            option.innerHTML = text;

            if (key == this.selectedCity)
                option.selected = "selected";

            citySelectView.appendChild(option);

            displayCitiesView = true;
        });

        func_displayView(citySelectView.id + "-widget", displayCitiesView);
    }

    /**
     * Send email verification email
     */
    sendEmailVerification() {

        let email = func_getView('email').value;

        var serverConnection = this.getServerConnection('send_email_verification');
        serverConnection.appendData("email", email);

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            var data = response.data;

            this.applyDefaultData(data);
            this.modalLoading.close();

            this.modalRealize.setHeader(trans('result.success'));
            this.modalRealize.setBody(response.message);
            this.modalRealize.setOnClose(() => {
                window.location.reload();
            });
            this.modalRealize.create();

        }.bind(this));

        serverConnection.connect();

    }
}
