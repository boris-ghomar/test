class BcSwarmApi {


    /**
     * constructor
     *
     * @param {string} apiBaseUrl
     * @param {objectView} googleRecaptchSiteKeyView
     */
    constructor(apiBaseUrl, googleRecaptchSiteKeyView = null, debugMode) {

        this.apiBaseUrl = apiBaseUrl;
        this.googleRecaptchSiteKeyView = googleRecaptchSiteKeyView;
        this.debugMode = debugMode;

        this.username = null;
        this.password = null;
        this.googleRecaptchaResponse = null;

        this.clientIp = null;
        this.getIp();

        this.loginData = null;

        this.progressbarMoveForwardFunc = null;

        this.ridCounter = 1;
        this.resetWebSocketData();

        window.onbeforeunload = (event) => {
            this.webSocketClose();
        };

        this.getInitialData();
    }

    /**
     * Set client credentials
     *
     * @param {string} username
     * @param {string} password
     * @param {string} googleRecaptchaResponse
     */
    setCredentials(username, password, googleRecaptchaResponse = null) {

        if (!this.isEmpty(username))
            this.username = username;

        if (!this.isEmpty(password))
            this.password = password;

        if (!this.isEmpty(googleRecaptchaResponse))
            this.googleRecaptchaResponse = googleRecaptchaResponse;
    }

    /**
     * Set progress bar move forward function
     * This input function is called every time a new step occurs in this class.
     *
     * @param {function} progressbarMoveForwardCallbackFunction
     */
    setProgressbarMoveForwardFunction(progressbarMoveForwardCallbackFunction = null) {

        this.progressbarMoveForwardFunc = progressbarMoveForwardCallbackFunction;
    }

    /**
     * Move progress bar froward
     */
    progressbarMoveForward() {

        try {
            if (this.progressbarMoveForwardFunc != null)
                this.progressbarMoveForwardFunc();
        } catch (error) {
            this.log(error, false, 'error');
        }

    }

    /**
     * Check if client credentials exists
     *
     * @returns {boolean}
     */
    isClientCredentialsExists() {

        if (!this.isEmpty(this.username) && !this.isEmpty(this.password)) {
            return true;
        } else {

            this.log("Error: Client credentials not exists.", false, 'error');
            return false;
        }
    }

    /**
     * Reset WebSocket used data
     */
    resetWebSocketData() {

        this.isWebSocketReady = false;
        this.sessionData = null;
        this.wsResponses = {};

    }

    /**
     * Get Client IP
     * Resource: https://www.ipify.org/
     */
    getIp() {

        try {

            var resolve = (data) => {

                this.log('Client IP: ' + data.ip);
                this.clientIp = data.ip;
            };

            fetch('https://api.ipify.org?format=json')
                .then(response => response.json())
                .then(data => resolve(data));
            /* .then(data => console.log('Client IP' + data.ip));*/

        } catch (error) {
            this.log('Client IP error: \n' + error, true, 'debug');
        }

    }

    /**
     * Get server connection handler base on action
     *
     * @param {string} action
     * @returns serverConnection handler
     */
    getServerConnection(action, onFailedCallbackFunc = null) {


        var serverConnection = new ServerConnection(this.apiBaseUrl);
        serverConnection.setSubUrl('bcswarm/' + action);
        serverConnection.setMethod("POST");
        serverConnection.appendHeader("locale", locale);
        serverConnection.appendData("clientIP", this.clientIp);
        serverConnection.appendData("webSocketUrl", this.webSocketUrl);

        serverConnection.setOnFailed(function (xhr, status, errorThrown) {
            var response = JSON.parse(xhr.responseText);

            this.progressbarMoveForward();

            var errorMessage = "";
            if (typeof response.message === "string") {
                errorMessage = response.message;
            } else {

                try {
                    /* if type of message is array, try to convert that to string list */
                    response.message.forEach(element => {
                        errorMessage += '* ' + element + "\n";
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

        }.bind(this));

        return serverConnection;

    }

    /**
     * Get initial data
     */
    getInitialData() {

        var serverConnection = this.getServerConnection('get_initial_data');

        serverConnection.setOnSuccess((responseJson) => {
            var response = JSON.parse(responseJson);

            this.progressbarMoveForward();

            var data = response.data;

            this.webSocketUrl = data.webSocketUrl;
            this.WebSocketUrlMain = data.webSocketUrl;
            this.WebSocketUrlAlternative = data.WebSocketUrlAlternative;
            this.siteId = data.siteId;
            this.language = data.language;

            this.webSocketOpen().then(() => {

                /* wait for load client IP */
                /*
                Disabled for check error:
                Error Code: 559
                Error Message: Session is In Processing RequestSession

                setTimeout(() => {
                     this.requestSession();
                }, 1000);
                */
            });

        });

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
     * Show log in console
     *
     * @param {any} message
     * @param {boolean} report
     * @param {string} type emergency|alert|critical|error|warning|notice|info|debug (Base on App\HHH_Library\general\php\LogCreator)
     */
    log(message, report = false, type = "info") {


        if (this.isEmpty(message))
            return;


        if (report) {
            /* Send log to server */
            try {

                var reposrtMsg = "";
                if (typeof message === 'string' || message instanceof String)
                    reposrtMsg = message;
                else
                    reposrtMsg = JSON.stringify(message);

                var serverConnection = this.getServerConnection('log');
                serverConnection.appendData('type', type);
                serverConnection.appendData('message', reposrtMsg);
                serverConnection.connect();
            } catch (error) {
            }
        }

        if (!this.debugMode)
            return;

        switch (type) {
            case "info":
            case "notice":
                console.log(message);
                break;
            case "emergency":
            case "critical":
            case "error":
            case "debug":
                console.error(message);
                break;
            case "alert":
            case "warning":
                console.warn(message);
                break;
            default:
                console.log(message);
                break;
        }
    }

    /**
     * Get error message from swarm response
     *
     * @param {object} commmand
     * @param {object} data
     * @returns {string} errorMessage
     */
    getSwarmErrorMessage(commmand, data) {

        return new Promise((resolve, reject) => {

            var serverConnectionFail = (error) => {
                reject(error);
            };

            var serverConnection = this.getServerConnection('get_error_message', serverConnectionFail);
            serverConnection.appendData('commmand', JSON.stringify(commmand));
            serverConnection.appendData('data', JSON.stringify(data));

            serverConnection.setOnSuccess((responseJson) => {
                var response = JSON.parse(responseJson);

                this.progressbarMoveForward();

                var data = response.data;

                resolve(data.message);
            });

            serverConnection.connect();
        });
    }

    /**
     * Check if websocket is open
     *
     * @returns boolean
     */
    isWebSocketOpen() {

        if (this.webSocket == null || this.webSocket == undefined)
            return false;

        if (this.webSocket.readyState != WebSocket.OPEN)
            return false;

        return true;
    }

    /**
     * Open WebSocket
     */
    webSocketOpen() {

        return new Promise((resolve, reject) => {

            if (this.isWebSocketOpen())
                resolve();

            try {

                if (this.isEmpty(this.webSocketUrl)) {

                    setTimeout(() => {
                        this.webSocketOpen().then(resolve, reject);
                    }, 1000);

                } else {

                    this.webSocket = new WebSocket(this.webSocketUrl);

                    this.webSocket.onopen = (event) => {

                        this.log('Websocket opened successfully.');
                        this.progressbarMoveForward();

                        this.isWebSocketReady = true;
                        resolve();
                    };

                    this.webSocket.onmessage = (event) => {

                        var responseJson = event.data;
                        var response = JSON.parse(responseJson);

                        this.log('New Response: \n' + responseJson);

                        /* Register response in wsResponses */
                        var rid = response.rid;
                        if (!this.isEmpty(rid)) {
                            this.wsResponses[rid] = responseJson;
                        }

                    };

                    this.webSocket.onerror = (event) => {

                        if (this.webSocketUrl == this.WebSocketUrlMain) {
                            /* Try to connect with alternative url */
                            this.webSocketUrl = this.WebSocketUrlAlternative;
                            this.webSocketOpen().then(resolve, reject);
                        } else {

                            var logMessage = JSON.stringify(event);
                            var logType = 'error';

                            var connectionCases = [
                                '{"isTrusted":true}',
                                '{"isTrusted":false}',
                            ];

                            if (connectionCases.includes(logMessage))
                                logType = 'warning';

                            this.log("webSocketOpen connection error: \n" + logMessage, true, logType);

                            reject(event);
                        }
                    };
                }

            } catch (error) {

                this.log("webSocketOpen connection exception error: \n" + error, true, 'error');

                this.resetWebSocketData();
                reject(error);
            }

        });


    }

    /**
     * Close webSocket
     */
    webSocketClose() {

        this.resetWebSocketData();

        if (this.isWebSocketOpen())
            this.webSocket.close();

        this.log('Websocket closed successfully.');
    }

    /**
     * Restart webSocket
     */
    webSocketRestart() {

        return new Promise((resolve, reject) => {

            this.webSocketClose();

            setTimeout(() => {
                this.webSocketOpen().then(resolve, reject);
            }, 1000);
        });
    }

    /**
     * Get Websocket Response
     *
     * @param {int} rid : Request ID
     * @returns
     */
    getWebsocketResponse(rid) {

        return new Promise((resolve, reject) => {

            var delay = 100;
            var delayCounter = delay;

            var interval = setInterval(() => {

                try {

                    if (!this.isWebSocketOpen()) {
                        this.log('getWebsocketResponse: Websocket was closed while waiting!', true, 'error');
                        clearInterval(interval);
                        window.location.reload();
                    }

                    var response = this.wsResponses[rid];

                    if (response != undefined) {

                        this.log('RID ' + rid + ' response: [Delay: ' + (delayCounter / 1000).toFixed(2) + ' s]\n' + response, true);
                        delete this.wsResponses[rid];
                        resolve(response);
                        clearInterval(interval);
                    } else {
                        this.log('Waiting for RID ' + rid + ' response.');
                        delayCounter += delay;
                    }

                } catch (error) {

                    this.log('getWebsocketResponse Exception Error: \n' + error, true, 'error');
                    reject(error);
                    clearInterval(interval);
                }

            }, delay);

        });
    }

    /**
     * Send data on websocket
     *
     * @param {string} data
     */
    webSocketSend(data) {

        return new Promise((resolve, reject) => {

            try {

                if (this.isWebSocketOpen() && this.isWebSocketReady) {

                    var rid = this.ridCounter++;
                    data.rid = rid;

                    /* Sending data on websocket */
                    var dataJson = JSON.stringify(data);

                    this.log('webSocketSend Sent: \n' + dataJson, true);
                    this.webSocket.send(dataJson);
                    this.progressbarMoveForward();

                    /* Creating get response promise and handle this function promise */
                    var getResponseResolve = (responseJson) => {

                        this.progressbarMoveForward();

                        try {

                            var response = JSON.parse(responseJson);

                            if (response.code == 0) {
                                /* Success */
                                resolve(response.data);
                            } else {
                                /* Failed */
                                reject(response);
                            }
                        } catch (error) {
                            this.log('webSocketSend Error: \n' + error, true, 'error');
                            reject(error);
                        }

                    }

                    this.getWebsocketResponse(rid).then(getResponseResolve, reject);
                }
                else {

                    var webSocketOpenResolve = () => {
                        this.webSocketSend(data).then(resolve, reject);
                    };

                    this.webSocketOpen().then(webSocketOpenResolve, reject);;
                }

            } catch (error) {

                this.log('webSocketSend Exception Error: \n' + error, true, 'error');
                reject(error);
            }

        });

    }


    /**
     * Session Request
     */
    requestSession() {

        return new Promise((resolve, reject) => {

            var commmand = {
                "command": "request_session",
                "params": {
                    "site_id": this.siteId,
                    "language": this.language,
                }
            };

            var wsSendResolve = (data) => {

                this.sessionData = data;
                var isRecaptchaEnabled = data.recaptcha_enabled;

                if (this.googleRecaptchSiteKeyView != null && isRecaptchaEnabled) {

                    this.googleRecaptchSiteKeyView.setAttribute("value", data.site_key);
                    this.googleRecaptchSiteKeyView.value = data.site_key;
                    this.googleRecaptchSiteKeyView.dispatchEvent(new Event('change'));
                }

                setTimeout(() => {
                    resolve(data);
                }, 500);
            }

            var wsSendReject = (data) => {
                /* Handle errors */

                if (data.code == 558) {
                    /* {"code":558,"msg":"Session already active","data":null} */

                    if (this.sessionData == null) {

                        var webSocketOpenResolve = () => {
                            this.requestSession().then(resolve, reject);
                        };

                        this.webSocketRestart().then(webSocketOpenResolve, reject);

                    } else
                        resolve(this.sessionData);
                } else
                    this.getSwarmErrorMessage(commmand, data).then(reject, reject);
            }

            this.webSocketSend(commmand).then(wsSendResolve, wsSendReject);
        });

    }

    /**
    * Login Request
    *
    * @param {string} username
    * @param {string} password
    */
    requestLogin(username, password, googleRecaptchaResponse) {

        this.username = username;
        this.password = password;

        return new Promise((resolve, reject) => {

            var commmand = {
                "command": "login",
                "params": {
                    "username": username,
                    "password": password,
                }
            };

            if (!this.isEmpty(googleRecaptchaResponse)) {

                commmand.params.g_recaptcha_response = googleRecaptchaResponse;
            }

            if (this.isEmpty(this.sessionData)) {

                var requestSessionResolve = () => {
                    this.requestLogin(username, password, googleRecaptchaResponse).then(resolve, reject);
                };

                this.requestSession().then(requestSessionResolve, reject);

            } else {

                /* Sending login request */
                var wsSendResolve = (data) => {

                    this.loginData = data;
                    resolve(data);
                }

                var wsSendReject = (data) => {
                    /* Handle errors */
                    /* {"code":27,"rid":"0","msg":"recaptcha verification needed","data":"6LfvKJMUAAAAAI0CmeiUEIurE3CFcCxwtf4SlUhk"} */

                    if (data.code == 5) {
                        /* {"code": 5, "rid": "0", "msg":"Invalid session", "data" : {} } */

                        /**
                         * The session information is checked before the login request,
                         *  so the websocket must be restarted so that it is not closed from the partner's side.
                         */
                        var webSocketOpenResolve = () => {
                            this.requestLogin(username, password, googleRecaptchaResponse).then(resolve, reject);
                        };

                        this.webSocketRestart().then(webSocketOpenResolve, reject);
                    } else
                        this.getSwarmErrorMessage(commmand, data).then(reject, reject);
                }

                this.webSocketSend(commmand).then(wsSendResolve, wsSendReject);
            }


        });
    }

    /**
     * Request get_user
     *
     */
    requestGetUser() {

        return new Promise((resolve, reject) => {

            if (!this.isClientCredentialsExists()) {

                /* Internal missing data; Developer Error. */
                reject('');
            }
            else {

                var commmand = {
                    "command": "get_user",
                    "params": {}
                };

                if (this.isEmpty(this.loginData)) {

                    var requestLoginResolve = () => {
                        this.requestGetUser().then(resolve, reject);
                    };

                    this.requestLogin(this.username, this.password, this.googleRecaptchaResponse).then(requestLoginResolve, reject);

                } else {

                    /* Sending get_user request */
                    var wsSendResolve = (data) => {

                        resolve(data);
                    }

                    var wsSendReject = (data) => {
                        /* Handle errors */

                        if (data.code == 12) {
                            /* {"code":12,"rid":"0","msg":"Invalid credentials","data":"Not logged in for command_get_user"} */

                            /**
                             * The "loginData" information is checked before the get_user request,
                             *  so the websocket must be restarted so that it is not closed from the partner's side.
                             */
                            var webSocketOpenResolve = () => {
                                this.requestGetUser().then(resolve, reject);
                            };

                            this.webSocketRestart().then(webSocketOpenResolve, reject);
                        } else
                            this.getSwarmErrorMessage(commmand, data).then(reject, reject);
                    }

                    this.webSocketSend(commmand).then(wsSendResolve, wsSendReject);
                }

            }

        });
    }

}
