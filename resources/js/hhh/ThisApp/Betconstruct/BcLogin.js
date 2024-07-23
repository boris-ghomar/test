class BcLogin {

    /**
     * constructor
     *
     * @param {string} apiBaseUrl
     * @param {boolean} debugMode
     */
    constructor(apiBaseUrl, debugMode = false) {

        this.apiBaseUrl = apiBaseUrl;
        this.debugMode = debugMode;
        this.modalLoading = new Modal_loading();

        this.bcSwarmApi = null;

        this.loginFormView = this.getView('login_form');
        this.errorContainerView = this.getView('jsErrorContainer');
        this.errorMessageView = this.getView('jsErrorMessage');

        this.progressbarData = null;
        this.setProgressbar(-1);

        this.btnSubmit = this.getView('btn_submit');
        this.displayView(this.btnSubmit.id, false);
        this.btnSubmit.addEventListener("click", function (event) {
            event.preventDefault();
            this.btnSumbitClicked();
        }.bind(this));

        this.getInitialData();
    }

    /**
     * Get initial data
     */
    getInitialData() {

        var onFailedFunc = function () {

            setTimeout(function () {
                window.location.reload();
            }, 500);
        }

        var serverConnection = this.getServerConnection('get_initial_data', false, onFailedFunc);

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            var data = response.data;

            this.targetRedirectUrl = data.targetRedirectUrl;

            this.appendBcSwarm(data.swarmSrc);

        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Check if value is empty
     *
     * @param {string|int} value
     * @returns
     */
    isEmpty(value) {

        if (value == undefined) return true;
        if (value == null) return true;
        if (value == "") return true;

        return false;
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
     * Show log in console
     *
     * @param {any} message
     * @param {string} type log|error|etc
     */
    log(message, type = "log") {

        if (!this.debugMode)
            return;

        if (this.isEmpty(message))
            return;

        switch (type) {
            case "log":
                console.log(message);
                break;
            case "error":
                console.error(message);
                break;
            case "warn":
                console.warn(message);
                break;
            default:
                console.log(message);
                break;
        }
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
     * Display error message
     *
     * @param {string} error
     */
    displayError(error) {

        if (!this.isEmpty(error)) {

            this.errorMessageView.innerHTML = error;
            this.displayView(this.errorContainerView.id, true);
        }
    }

    /**
     * Appned Google Recaptcha
     */
    appendGoogleRecaptcha() {

        if (this.googleRecaptchSiteKeyView == undefined) {

            this.googleRecaptchSiteKeyView = this.getView('googleRecaptchSiteKey');
            this.gRecaptchaResponseView = this.getView('g_recaptcha_response');

            this.googleRecaptchSiteKeyView.addEventListener('change', function (event) {

                this.appendGoogleRecaptcha();
            }.bind(this));
        }

        this.googleRecaptchSiteKey = this.googleRecaptchSiteKeyView.value;

        if (!this.isEmpty(this.googleRecaptchSiteKey)) {

            var scriptSrc = "https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=" + this.googleRecaptchSiteKey;

            var googleRecaptchaScriptId = "google_recaptcha";
            var googleRecaptchaScriptObj = this.getView(googleRecaptchaScriptId);

            if (googleRecaptchaScriptObj == null) {

                var script = document.createElement("script");
                script.id = googleRecaptchaScriptId;
                script.src = scriptSrc;
                script.setAttribute("async", '');
                script.setAttribute("defer", '');
                document.body.appendChild(script);
            } else {
                googleRecaptchaScriptObj.src = scriptSrc;
            }


        }
    }

    /**
     * Appned Bc Swarm Api class
     *
     * @param {string} swarmSrc
     */
    appendBcSwarm(swarmSrc) {

        var script = document.createElement("script");
        script.src = swarmSrc;
        script.addEventListener("load", function (event) {

            this.appendGoogleRecaptcha();

            this.bcSwarmApi = new BcSwarmApi(this.apiBaseUrl, this.googleRecaptchSiteKeyView, this.debugMode);
            this.bcSwarmApi.setProgressbarMoveForwardFunction(this.progressbarMoveForward.bind(this));


            setTimeout(() => {
                this.displayView(this.btnSubmit.id, true);
            }, 3000);

        }.bind(this));
        document.body.appendChild(script);
    }

    /**
     * Get server connection handler base on action
     *
     * @param {string} action
     * @param {boolean} runLoading
     * @returns serverConnection handler
     */
    getServerConnection(action, runLoading = false, onFailedCallbackFunc = null) {

        if (runLoading)
            this.modalLoading.show();

        var serverConnection = new ServerConnection(this.apiBaseUrl);
        serverConnection.setSubUrl('bclogin/' + action);
        serverConnection.setMethod("POST");
        serverConnection.appendHeader("locale", locale);

        serverConnection.setOnFailed(function (xhr, status, errorThrown) {
            var response = JSON.parse(xhr.responseText);

            var errorMessage = "";
            if (typeof response.message === "string") {
                errorMessage = response.message;
            } else {

                try {
                    /* if type of message is array, try to convert that to string list */
                    response.message.forEach(element => {
                        errorMessage += '* ' + element + "\n<br>";
                    });
                } catch (error) {
                    errorMessage = JSON.stringify(response.message, null, 2);
                }
            }

            if (errorMessage == "exception.CSRF token mismatch.")
                window.location.reload();


            if (response.data != null) {

                if (response.data.refreshPage === true)
                    window.location.reload();
            }

            this.log(errorMessage);

            if (onFailedCallbackFunc != null)
                onFailedCallbackFunc(errorMessage);

            if (runLoading)
                this.modalLoading.close();

        }.bind(this));

        return serverConnection;

    }


    /**
     * Form sumbit button clicked
     *
     */
    btnSumbitClicked() {

        var username = this.getView('username').value.trim();
        var password = this.getView('password').value.trim();

        if (this.isEmpty(username)) {
            /* var usernameReqired definded in HTML*/
            this.displayError(usernameReqired);
            return;
        }
        if (this.isEmpty(password)) {
            /* var usernameReqired definded in HTML*/
            this.displayError(passwordReqired);
            return;
        }

        this.modalLoading.show();
        this.setupProgressbarData(0, 10, 10);

        if (!this.isEmpty(this.googleRecaptchSiteKey)) {

            try {
                grecaptcha.ready(function () {
                    grecaptcha.execute(this.googleRecaptchSiteKey, {
                        action: 'submit'
                    }).then(function (token) {

                        this.gRecaptchaResponseView.value = token;

                        this.collectClientData(username, password, token);

                    }.bind(this));
                }.bind(this));

            } catch (error) {
                /* goole recaptcha not working */
                this.collectClientData(username, password, null);
            }

        } else {
            this.collectClientData(username, password, null);
        }

    }

    /**
     * Collect and validate client data
     *
     * @param {string} username
     * @param {string} password
     * @param {string} gRecaptchaResponse
     */
    collectClientData(username, password, gRecaptchaResponse = null) {

        this.setupProgressbarData(20, 45, 5);
        this.progressbarMoveForward();

        var requestReject = (error) => {
            this.displayError(error);
            this.modalLoading.close();
            this.setProgressbar(-1);
        };

        /* Request login */
        this.bcSwarmApi.requestLogin(username, password, gRecaptchaResponse)
            .then((data) => {

                this.setupProgressbarData(45, 75, 5);
                this.progressbarMoveForward();

                var loginData = JSON.stringify(data);

                /* Request get_user */
                this.bcSwarmApi.requestGetUser().then((data) => {

                    this.setupProgressbarData(75, 95, 5);
                    this.progressbarMoveForward();

                    var getUserData = JSON.stringify(data);

                    /* Send data to server */

                    var serverConnection = this.getServerConnection('attempt', false, requestReject);
                    serverConnection.appendData('username', username);
                    serverConnection.appendData('password', password);
                    serverConnection.appendData('remember', this.getView('remember').value);
                    serverConnection.appendData('loginData', loginData);
                    serverConnection.appendData('getUserData', getUserData);

                    serverConnection.setOnSuccess((responseJson) => {
                        var response = JSON.parse(responseJson);

                        this.setProgressbar(100);

                        if (!func_isEmpty(this.targetRedirectUrl))
                            window.location = this.targetRedirectUrl;
                        else {

                            if ('referrer' in document) {
                                window.location = document.referrer;
                            } else {
                                window.location.reload();
                            }
                        }

                    });

                    serverConnection.connect();

                }, requestReject);

            }, requestReject);
    }
}
