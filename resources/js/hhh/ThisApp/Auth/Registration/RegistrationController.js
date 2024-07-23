class RegistrationController {

    constructor(apiBaseUrl) {

        this.apiBaseUrl = apiBaseUrl;
        this.debugMode = false;

        this.modalLoading = new Modal_loading();

        this.provincesCitiesCollection = [];
        this.provinceSelectView = func_getView('province_internal');
        this.citySelectView = func_getView('city_internal');

        if (!func_isEmpty(this.citySelectView)) {

            this.selectedCity = func_getView(this.citySelectView.id + '_selectedItem').value;
            this.provinceSelectView.addEventListener('change', (event) => { this.provinceChanged() });
        }

        this.provinceChanged();
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
        serverConnection.setSubUrl('registration/' + action);
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
     * Province dropdown changed
     */
    provinceChanged() {

        if (func_isEmpty(this.citySelectView))
            return;

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
