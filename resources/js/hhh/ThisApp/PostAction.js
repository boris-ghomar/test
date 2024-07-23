class PostAction {

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

        var modalRealize = new ModalRealize('modalRealize_PostAction');
        modalRealize.setHeader(trans('result.failed'));
        this.modalRealize = modalRealize

        var modalLoginRequired = new ModalCustom('modalLoginRequired_PostAction');
        modalLoginRequired.setHeader(trans('result.failed'));
        modalLoginRequired.setBody(loginReqired); /* Defined in HTML Script */
        modalLoginRequired.setCustomBtnLabel(signInBtnLabel); /* Defined in HTML Script */
        modalLoginRequired.setOnCustomBtnPressed(function () {
            window.location.href = loginPageUrl; /* Defined in HTML Script */
        });
        this.modalLoginRequired = modalLoginRequired
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
     * Check if client needs login.
     *
     * @param {string} serverError
     * @returns {boolean}
     */
    isLoginRequired(serverError) {

        return serverError == 'thisApp.Errors.loginRequired';
    }

    /**
     * Go to anchor link in URL
     */
    goToAnchorLink() {

        var urlSplit = document.location.href.split('#');

        var urlSplitLength = urlSplit.length;

        if (urlSplitLength > 1) {

            var anchorId = urlSplit[urlSplitLength - 1];

            var anchorElement = document.getElementById(anchorId);
            if (is_ElementExist(anchorElement)) {

                anchorElement.scrollIntoView();
                anchorElement.getElementsByClassName("comment-box")[0].classList.add(
                    "selected-comment");
            }
        }

    }

    /**
     * Get server connection handler base on action
     *
     * @param {string} action
     * @returns serverConnection handler
     */
    getServerConnection(action, onFailedCallbackFunc = null) {

        this.modalLoading.show();

        var serverConnection = new ServerConnection(this.apiBaseUrl);
        serverConnection.setSubUrl('user_actions/' + action);
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

            if (this.isLoginRequired(response.error)) {

                this.modalLoginRequired.create();
            } else {

                this.modalRealize.setBody(errorMessage);
                this.modalRealize.create();
            }

            if (onFailedCallbackFunc != null)
                onFailedCallbackFunc(response, errorMessage);

            this.modalLoading.close();

        }.bind(this));

        return serverConnection;

    }

    /**
     * Like action
     *
     * @param {string} likableViewId
     * @param {string} payload
     */
    like(likableViewId, payload) {

        var likableIcon = document.getElementById(likableViewId + "_icon");
        var likableCounter = document.getElementById(likableViewId + "_counter");

        var serverConnection = this.getServerConnection('like');
        serverConnection.appendData("key", payload);

        serverConnection.setOnSuccess((response) => {
            response = JSON.parse(response);

            toggleElementClass(likableIcon.id, 'fa-solid');
            toggleElementClass(likableIcon.id, 'liked');

            var likesCount = response.data.likesCount;
            likableCounter.innerHTML = likesCount;

            this.modalLoading.close();
        });

        serverConnection.connect();
    }

    /**
     * Comment action
     *
     * @param {string} commentableViewId
     * @param {string} payload
     */
    comment(commentableViewId, payload) {

        var commentableCounter = document.getElementById(commentableViewId + "_counter");
        var commentInput = document.getElementById(commentableViewId + "_comment");

        var serverConnection = this.getServerConnection('comment');
        serverConnection.appendData("key", payload);
        serverConnection.appendData("comment", commentInput.value);

        serverConnection.setOnSuccess((response) => {
            response = JSON.parse(response);

            var commentsCount = response.data.commentsCount;
            var reload = response.data.reload;
            var anchorLink = response.data.anchorLink;

            commentableCounter.innerHTML = commentsCount;
            commentInput.value = "";

            var modalRealize = this.modalRealize;
            modalRealize.setHeader(successCommentTitle);
            modalRealize.setBody(response.message);

            this.modalLoading.close();
            this.modalRealize.create();
            if (reload) {
                history.replaceState(null, null, anchorLink);
                window.location.href = anchorLink;
                window.location.reload();
            }
        });

        serverConnection.connect();
    }
}
