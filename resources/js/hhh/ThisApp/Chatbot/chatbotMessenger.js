class ChatbotMessenger {

    constructor(apiBaseUrl, payload, mainCanvasViewId, classHandlerVar) {

        this.apiBaseUrl = apiBaseUrl;
        this.payload = payload;
        this.classHandlerVar = classHandlerVar;
        this.LastServerMessage = null;

        /* Hide site footer */
        this.displayView('site_footer', false);
        this.displayView('SupportChatbotIcon', false);

        this.modalZoomImage = new ModalZoomImage();

        this.MESSAGE_TYPE_TEXT = "Text";
        this.MESSAGE_TYPE_IMAGE = "Image";
        this.MESSAGE_TYPE_BUTTON = "Button";
        this.MESSAGE_TYPE_INPUT = "Input";
        this.MESSAGE_TYPE_FILTER = "Filter";
        this.MESSAGE_TYPE_BOT_ACTION = "BotAction";

        /* Templates Views */
        this.mainCanvas = this.getView(mainCanvasViewId);
        this.loadingTemplate = this.getView("LoadingTemplate");
        this.messageTemplate = this.getView("MessageTemplate");
        this.chatbotProfileTemplate = this.getView("ChatbotProfileTemplate");
        this.userProfileTemplate = this.getView("UserProfileTemplate");

        /* Translation variables*/
        this.errorMsgImageRemoved = document.getElementById('ChatbotMessenger_ImageRemoved').value;

        this.clearCanvas(this.mainCanvas);
        this.getInitialData();
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
                console.log(error);
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

        if (value == undefined) return true;
        if (value == null) return true;
        if (value == "") return true;

        return false;
    }

    /**
     * Change not loaded image with error text message
     *
     * @param {viewObject} imgObj
     */
    imageNotLoaded(imgObj) {

        var errorMsgImageRemoved = document.getElementById('ChatbotMessenger_ImageRemoved').value;

        var errorMessageElement = '<i class="fa-regular fa-image"></i><br>';
        errorMessageElement += '<small class="text-muted">' + errorMsgImageRemoved + '</small>';

        imgObj.parentNode.innerHTML = errorMessageElement;
    }

    /**
     * Scroll chat page down
     */
    scrolldown() {

        var scroller = this.mainCanvas;

        setTimeout(function () {

            scroller.scrollIntoView(false);
        }, 500);

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
     * Get server connection handler base on action
     *
     * @param {string} action
     * @returns serverConnection handler
     */
    getServerConnection(action, onFailedCallbackFunc = null) {

        this.displayBotWaiting(true);

        var serverConnection = new ServerConnection(this.apiBaseUrl);
        serverConnection.setSubUrl('chatbot/messenger/' + action);
        serverConnection.setMethod("POST");
        serverConnection.appendHeader("locale", locale);
        serverConnection.appendData("payload", this.payload);


        serverConnection.setOnFailed(function (xhr, status, errorThrown) {
            var response = JSON.parse(xhr.responseText);

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

            console.log(errorMessage);

            if (!this.isEmpty(response.data)) {

                if (this.booleanValue(response.data.refreshPage))
                    window.location.reload();
            }

            this.displayBotWaiting(false);

            /* ignore showin exception errors */
            var ignoreMessage = false;
            var ignoreList = ['exception', 'server error'];

            ignoreList.forEach(item => {

                if (errorMessage.toLowerCase().includes(item.toLowerCase()))
                    ignoreMessage = true;
            });

            if (!ignoreMessage)
                this.addTextMessageView(true, errorMessage);

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
        if (csrfToken != undefined) {

            var csrfTokenElement = document.getElementsByName('csrf-token');
            if (csrfTokenElement.length > 0) {

                csrfTokenElement = csrfTokenElement[0];

                csrfTokenElement.content = csrfToken;
            }
        }
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
     * Add message sender profile view
     *
     * @param {boolean} isBotMessege
     */
    addProfileView(isBotMessege) {

        var profileTemplate = "";
        var data = null;

        var allowToInsertProfileView = false;

        if (this.isBotSentLastMessage != isBotMessege) {
            allowToInsertProfileView = true;
            this.isBotSentLastMessage = isBotMessege;
        }

        if (allowToInsertProfileView) {

            if (isBotMessege) {

                profileTemplate = this.chatbotProfileTemplate;
                data = this.chatbotProfile;
            } else {
                profileTemplate = this.userProfileTemplate;
                data = this.clientProfile;
            }

            this.addTemplateToView(this.mainCanvas, profileTemplate, null, data);
        }


    }

    /**
     * Add text message view to chat page view
     *
     * @param {boolean} isBotMessage
     * @param {string} message
     * @param {string} id
     */
    addTextMessageView(isBotMessage, message, id = null) {

        var data = {
            id: (id == null) ? this.getTemporaryID('text_msg_') : id,
            is_bot_message: isBotMessage,
            type: this.MESSAGE_TYPE_TEXT,
            content: { Message: message },
            is_passed: true
        };

        this.addMessageView(data);
    }

    /**
     *  Add message to chat page view
     *
     *  Message types: BotResponse (Text|Image|File), User answer, server errors
     *
     * @param {object} messageData
     */
    addMessageView(messageData) {

        if (messageData == null)
            return;

        var messageId;
        var isBotMessage;
        var messageType;
        var messageContent;
        var isPassed;

        var messageView = null;
        try {

            messageId = messageData.id;
            isBotMessage = messageData.is_bot_message;
            messageType = messageData.type;
            messageContent = messageData.content;
            isPassed = messageData.is_passed;

            /* Ignore creating message view */
            if (messageType == this.MESSAGE_TYPE_FILTER)
                return;
            if (messageType == this.MESSAGE_TYPE_BOT_ACTION)
                return;


            this.addProfileView(isBotMessage);

            switch (messageType) {

                case this.MESSAGE_TYPE_TEXT:
                    messageView = this.getMsgTextView(messageContent);
                    break;

                case this.MESSAGE_TYPE_IMAGE:
                    messageView = this.getMsgImageView(messageContent);
                    break;

                case this.MESSAGE_TYPE_BUTTON:
                    messageView = this.getMsgButtonView(messageContent);
                    break;

                case this.MESSAGE_TYPE_INPUT:
                    messageView = this.getMsgInputView(messageContent, isPassed);
                    break;

                case this.MESSAGE_TYPE_FILTER:
                    /*No need to display anything */
                    break;

                default:
                    break;
            }
        } catch (error) {
            messageView = null;
            console.log(error);
        }

        if (messageView != null) {

            var data = {
                isBotMessage: isBotMessage,
                messageType: messageType,
                messageView: messageView,
            };

            this.addTemplateToView(this.mainCanvas, this.messageTemplate, null, data);
        }
    }

    /**
     * Get text message view
     *
     * @param {object} messageContent
     * @returns {string|null}
     */
    getMsgTextView(messageContent) {

        var messageView = null;

        var message = messageContent.Message.replaceAll('\n', '<br>');
        if (message != null && message != "")
            messageView = '<small class="text-muted">' + message + '</small>';

        return messageView;
    }

    /**
     * Get image message view
     *
     * @param {object} messageContent
     * @returns {string|null}
     */
    getMsgImageView(messageContent) {

        var messageView = null;

        var fileName = messageContent.FileName;
        if (fileName != null && fileName != "") {

            var imagUrl = this.imageResponsePath + fileName;
            messageView = '<img class="chat-image" src="' + imagUrl + '"';
            messageView += ' ' + 'onerror="' + this.classHandlerVar + '.imageNotLoaded(this);"';
            messageView += ' ' + 'onclick="' + this.classHandlerVar + '.zoomImage(this);" ';
            messageView += ' >';
        }

        return messageView;
    }

    /**
     * Get button message view
     *
     * @param {object} messageContent
     * @returns {string|null}
     */
    getMsgButtonView(messageContent) {

        var messageView = null;

        var btnType = messageContent.Type; /* GoToStep|OpenUrl */
        var btnTitle = messageContent.Title;

        if (btnType == "GoToStep") {
            var targetStep = messageContent.TargetStep;
            var onClickFunc = this.classHandlerVar + ".getNextStepMessage('" + targetStep + "');";
            messageView = '<button type="button" class="btn btn-primary" onclick="' + onClickFunc + '" >' + btnTitle + '</button>';
        } else if (btnType == "OpenUrl") {
            var targetUrl = messageContent.TargetUrl;
            var onClickFunc = "window.open('" + targetUrl + "', '_blank');";
            messageView = '<button type="button" class="btn btn-success" onclick="' + onClickFunc + '" >' + btnTitle + '</button>';
        }

        return messageView;
    }

    /**
     * Get input message view
     *
     * @param {object} messageContent
     * @param {boolean} isPassed
     * @returns {string|null}
     */
    getMsgInputView(messageContent, isPassed) {

        var inputType = messageContent.Type; /* Number|OneLineText|MultipleLineText|Image */
        var template = null;

        switch (inputType) {
            case "Number":
                template = isPassed ? this.getView("InputNumberPassedTemplate") : this.getView("InputNumberTemplate");
                break;
            case "OneLineText":
                template = isPassed ? this.getView("InputOneLineTextPassedTemplate") : this.getView("InputOneLineTextTemplate");
                break;
            case "MultipleLineText":
                template = isPassed ? this.getView("InputMultipleLineTextPassedTemplate") : this.getView("InputMultipleLineTextTemplate");
                break;
            case "Image":
                template = isPassed ? this.getView("InputImagePassedTemplate") : this.getView("InputImageTemplate");
                break;

            default:
                return null;
        }

        return this.modifyTempalte(template, null, messageContent);

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
            var newView = "<div id='" + id + "'>";
            newView += template_innerHTML;
            newView += "</div>";

            parentView.innerHTML += newView;
        }

        this.scrolldown();
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


        if (template.id == "MessageTemplate") {

            /* App\Enums\Chatbot\Messenger\ChatbotMessageTypesEnum: Text|Image|Button|Input */
            var messageViewKey = "MessageTemplate_MessageView";

            var isBotMessageKey = "MessageTemplate_isBotMessage";
            var messageTypeKey = "MessageTemplate_MessageType";

            var style1Key = "MessageTemplate_Style1";

            var isBotMessage = data.isBotMessage;
            var messageType = data.messageType;
            var messageView = data.messageView;

            template_innerHTML = template_innerHTML.replaceAll(isBotMessageKey, isBotMessage);
            template_innerHTML = template_innerHTML.replaceAll(messageTypeKey, messageType);

            template_innerHTML = template_innerHTML.replaceAll(style1Key, isBotMessage ? "me-5 flex-row-reverse" : "ms-5");

            template_innerHTML = template_innerHTML.replaceAll(messageViewKey, messageView);

        }
        else if (template.id == "InputNumberTemplate" || template.id == "InputNumberPassedTemplate"
            || template.id == "InputOneLineTextTemplate" || template.id == "InputOneLineTextPassedTemplate"
            || template.id == "InputMultipleLineTextTemplate" || template.id == "InputMultipleLineTextPassedTemplate"
            || template.id == "InputImageTemplate" || template.id == "InputImagePassedTemplate") {

            var templateViewIdKey = "TemplateViewID";
            var labelKey = "InputTemplate_Label";
            var placeholderKey = "InputTemplate_Placeholder";
            var submitFuncKey = "InputTemplate_SubmitFunc";
            var userAnswerKey = "InputPassedTemplate_UserAnswer";
            var classHandlerVarKey = "TemplateClassHandlerVar";
            var inputImageViewKey = "InputImageTemplate_View";

            var viewId = this.getTemporaryID();

            var content = data.Data;
            var title = this.replaceNull(content.Title);
            var description = this.replaceNull(content.Description);

            if (description != null && description != "")
                title += "<br>( " + description + " )";

            template_innerHTML = template_innerHTML.replaceAll(templateViewIdKey, viewId);
            template_innerHTML = template_innerHTML.replaceAll(labelKey, title);
            template_innerHTML = template_innerHTML.replaceAll(placeholderKey, this.replaceNull(content.Placeholder));
            template_innerHTML = template_innerHTML.replaceAll(userAnswerKey, this.replaceNull(data.UserAnswer));
            template_innerHTML = template_innerHTML.replaceAll(classHandlerVarKey, this.classHandlerVar);

            if (template.id == "InputImagePassedTemplate") {

                var inputImageView = this.isEmpty(data.UserAnswer) ? "" : '<img class="chat-image" src="' + this.userInputImagePath + data.UserAnswer + '" onclick="' + this.classHandlerVar + '.zoomImage(this);" >';
                template_innerHTML = template_innerHTML.replaceAll(inputImageViewKey, inputImageView);
            }

            var submitFunc = this.classHandlerVar + ".submitUserInput('" + content.StepId + "','" + viewId + "')";
            template_innerHTML = template_innerHTML.replaceAll(submitFuncKey, submitFunc);

        }
        else if (template.id == "ChatbotProfileTemplate" || template.id == "UserProfileTemplate") {
            var displayNameKey = "ProfileTemplate_DisplayName";
            var profileImageSrcKey = "profile_image_src";

            template_innerHTML = template_innerHTML.replaceAll(displayNameKey, data.dispalyName);
            template_innerHTML = template_innerHTML.replaceAll(profileImageSrcKey, 'src="' + data.profileImage + '"');
        }


        return template_innerHTML;
    }

    /**
     * Display Bot waiting
     *
     * @param {boolean} display
     */
    displayBotWaiting(display = true) {

        var viewId = "botWaiting";

        /* Remove last botWaiting elements */
        this.removeView(viewId);

        if (display)
            this.addTemplateToView(this.mainCanvas, this.loadingTemplate, viewId);

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
     * User input image file Changed.
     * User selcted image for upload
     *
     * @param {objectView} fileInputObj
     * @param {string} templateViewID
     */
    userinputImageFileChanged(fileInputObj, templateViewID) {

        var fakePath = fileInputObj.value;

        var fileName = "";
        try {
            fileName = fakePath.split("\\").pop();
        } catch (error) {
            fileName = fakePath;
        }

        var fileNameInput = this.getView('MsgInputFileName_' + templateViewID);
        fileNameInput.setAttribute("value", fileName);

        this.displayView("MsgSubmitBtn_" + templateViewID, true);
    }

    /**
     * Get initial data
     */
    getInitialData() {

        var serverConnection = this.getServerConnection('get_initial_data');

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            var data = response.data;

            this.displayBotWaiting(false);

            this.imageResponsePath = data.imagesBasePath.imageResponse;
            this.userInputImagePath = data.imagesBasePath.userInputImage;

            this.chatbotProfile = data.chatbotProfile;
            this.clientProfile = data.clientProfile;

            this.getPreviousMessages();

        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Get previous messages
     */
    getPreviousMessages() {

        var serverConnection = this.getServerConnection('get_previous_messages');

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            var data = response.data;

            this.displayBotWaiting(false);

            var messages = data.messages;
            if (messages != undefined)
                messages.forEach(message => {

                    this.addMessageView(message);
                    this.LastServerMessage = message;
                });

            this.applyDefaultData(data);
            this.getNextStepMessage();

        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Get next step message
     */
    getNextStepMessage(stepId = null) {

        var serverConnection;

        var onFailedCallbackFunc = (response) => {

            try {
                var goToStep = response.data.GoToStep;

                if (!this.isEmpty(goToStep)) {
                    this.getNextStepMessage(goToStep);
                }

            } catch (error) {
                console.warn(error);
            }

        };

        if (stepId == null) {
            serverConnection = this.getServerConnection('get_next_step_message', onFailedCallbackFunc);
        } else {
            serverConnection = this.getServerConnection('go_to_step', onFailedCallbackFunc);
            serverConnection.appendData("stepId", stepId);
        }

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);
            var data = response.data;

            if (data != null) {

                this.LastServerMessage = data;

                var delay = data.delay;
                if (delay == undefined)
                    delay = 0;

                setTimeout(function () {

                    this.displayBotWaiting(false);

                    this.addMessageView(data);
                    this.applyDefaultData(data);

                    this.getNextStepMessage();

                }.bind(this), delay * 1000);

            } else {
                this.displayBotWaiting(false);
            }

        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Submit user input
     * @param {string} stepId
     * @param {string} viewId
     */
    submitUserInput(stepId, viewId) {

        var isValidRequest = true;
        if (stepId == null)
            isValidRequest = false;

        var inputView = this.getView("MsgInput_" + viewId);
        var submitBtnView = this.getView("MsgSubmitBtn_" + viewId);
        var statusView = this.getView("MsgStatusView_" + viewId);

        if (!is_ElementExist(inputView))
            isValidRequest = false;

        var userInput = inputView.value;

        var inputViewTagName = inputView.tagName.toLowerCase();

        if (inputViewTagName == "input" && inputView.type == "file") {
            userInput = inputView.files[0];

            var fileUploadInfoView = this.getView("MsgFileUploadInfo_" + viewId);
            fileUploadInfoView.parentNode.removeChild(fileUploadInfoView);

            if (userInput == undefined) {
                /* No file selected */
                userInput = null;
            } else {

                var fileReader = new FileReader();
                fileReader.addEventListener("load", function () {
                    var inputImageSrc = fileReader.result;
                    var imagePreview = document.getElementById('MsgImagePreview_' + viewId);

                    imagePreview.innerHTML = '<img class="chat-image" src="' + inputImageSrc + '" onclick="' + this.classHandlerVar + '.zoomImage(this);" >';
                    this.scrolldown();
                }.bind(this));

                fileReader.readAsDataURL(userInput);
            }

        }
        else if (inputViewTagName == "textarea")
            inputView.innerHTML = userInput;
        else
            inputView.setAttribute("value", userInput);

        if (isValidRequest) {

            var statusProccess = '<i class="fa-duotone fa-spinner fa-spin-pulse"></i>';
            var statusSuccess = '<i class="fa-duotone fa-check text-success"></i>';
            var statusFail = '<i class="fa-solid fa-xmark text-danger"></i>';

            submitBtnView.parentNode.removeChild(submitBtnView);
            inputView.disabled = true;
            statusView.innerHTML = statusProccess;

            var onFailedCallbackFunc = function () {

                this.getView(statusView.id).innerHTML = statusFail;
                this.addMessageView(this.LastServerMessage);

            }.bind(this);

            var serverConnection = this.getServerConnection('submit_user_input', onFailedCallbackFunc);
            serverConnection.appendData("stepId", stepId);
            serverConnection.appendData("userInput", userInput);

            serverConnection.setOnSuccess(function (responseJson) {
                var response = JSON.parse(responseJson);
                var data = response.data;

                if (data != null) {

                    this.displayBotWaiting(false);
                    this.applyDefaultData(data);

                    this.getView(statusView.id).innerHTML = statusSuccess;

                    this.getNextStepMessage();

                } else {
                    this.displayBotWaiting(false);
                }

            }.bind(this));

            serverConnection.connect();
        } else {
            this.displayBotWaiting(false);
        }

    }

    /**
     * Close chat
     */
    closeChat() {

        var serverConnection;

        serverConnection = this.getServerConnection('close_chat');

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);
            var data = response.data;

            if (data != null) {

                var redirectUrl = data.redirectUrl;

                this.displayBotWaiting(false);

                if (!this.isEmpty(redirectUrl))
                    window.open(redirectUrl, '_parent');

            } else {
                this.displayBotWaiting(false);
            }

        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Zoom in and out image
     *
     * @param {objectView} imagView
     */
    zoomImage(imagView) {

        this.modalZoomImage.setImage(imagView.src);
        this.modalZoomImage.show();
    }

}
