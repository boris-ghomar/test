class TicketMessenger {

    constructor(apiBaseUrl, payload, mainCanvasViewId, classHandlerVar) {

        this.apiBaseUrl = apiBaseUrl;
        this.payload = payload;
        this.classHandlerVar = classHandlerVar;
        this.profiles = {};
        this.debugMode = false;
        this.lastMessageSenderId = -1;
        this.lastMessageId = null;

        this.fetchMessageDelay = 5000; /*milliseconds */

        this.modalZoomImage = new ModalZoomImage();

        /* Hide site footer */
        this.displayView('site_footer', false);

        this.MESSAGE_TYPE_TEXT = "Text";
        this.MESSAGE_TYPE_IMAGE = "Image";

        this.MESSAGE_TYPE_TICKET_IMAGE = "TicketImage";
        this.MESSAGE_TYPE_CHATBOT_IMAGE = "ChatbotImage";

        this.mainCanvas = this.getView(mainCanvasViewId);
        this.loadingTemplate = this.getView("LoadingTemplate");
        this.messageTemplate = this.getView("MessageTemplate");
        this.userProfileTemplate = this.getView("UserProfileTemplate");
        this.replierProfileTemplate = this.getView("ReplierProfileTemplate");

        this.clearCanvas(this.mainCanvas);
        this.getInitialData();
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
     * Scroll chat page down
     */
    scrolldown() {

        var scroller = this.mainCanvas;

        setTimeout(() => {
            scroller.scrollTo(0, scroller.scrollHeight);
        }, 200);

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
     * Display Bot waiting
     *
     * @param {boolean} display
     */
    displayWaiting(display = true) {

        var viewId = "WaitingView";

        /* Remove last WaitingView elements */
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
     * Add text message view to chat page view
     *
     * @param {string} message
     */
    addLocalMessageView(message) {

        let botIcon = '<i class="fa fa-message-bot" ></i><br>';

        var data = {
            id: this.getTemporaryID('text_msg_'),
            userId: null,
            type: this.MESSAGE_TYPE_TEXT,
            content: botIcon + message,
            updated_at: "",
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
        var userId;
        var messageType;
        var messageContent;
        var messageTime;

        var messageViewType;
        var messageView = null;
        try {

            messageId = messageData.id;
            userId = messageData.user_id;
            messageType = messageData.type;
            messageContent = messageData.content;
            messageTime = messageData.updated_at;

            messageViewType = messageType;

            if (!this.isEmpty(userId))
                this.addProfileView(userId);

            switch (messageType) {

                case this.MESSAGE_TYPE_TEXT:
                    messageView = this.getMsgTextView(messageContent);
                    messageViewType = messageType;
                    break;

                case this.MESSAGE_TYPE_TICKET_IMAGE:
                    messageView = this.getMsgImageView(this.ticketImagePath + messageContent);
                    messageViewType = this.MESSAGE_TYPE_IMAGE;
                    break;
                case this.MESSAGE_TYPE_CHATBOT_IMAGE:
                    messageView = this.getMsgImageView(this.chatbotUserInputImagePath + messageContent);
                    messageViewType = this.MESSAGE_TYPE_IMAGE;
                    break;

                default:
                    break;
            }
        } catch (error) {
            messageView = null;
            this.log(error, false, 'error');
        }

        if (messageView != null) {

            var data = {
                userId: userId,
                messageType: messageViewType,
                messageView: messageView,
                messageTime: messageTime,
            };

            this.addTemplateToView(this.mainCanvas, this.messageTemplate, messageId, data);
        }
    }

    /**
     * Add message sender profile view
     *
     * @param {int} userId
     */
    addProfileView(userId) {


        var profileTemplate = "";
        var viewId = this.getTemporaryID('profileView_');

        var data = {
            userId: userId,
            viewId: viewId,
        };

        var allowToInsertProfileView = false;

        if (this.lastMessageSenderId != userId) {
            allowToInsertProfileView = true;
            this.lastMessageSenderId = userId;
        }

        if (allowToInsertProfileView) {

            var isUserMessage = this.userId == userId;

            profileTemplate = isUserMessage ? this.userProfileTemplate : this.replierProfileTemplate;

            this.addTemplateToView(this.mainCanvas, profileTemplate, viewId, data);
        }
    }

    /**
     * Convert link inside the text to clickable
     *
     * @param {string} text
     * @returns
     */
    convertLinkToClickable(text) {

        if (!this.isEmpty(text)) {

            var urlRegex = /(https?:\/\/[^\s]+)/g;
            return text.replace(urlRegex, function (url) {
                return '<a href="' + url + '" target="_blank">' + url + '</a>';
            });
        }

        return text;
    }

    /**
     * Get text message view
     *
     * @param {object} messageContent
     * @returns {string|null}
     */
    getMsgTextView(messageContent) {

        var messageView = null;

        var message = messageContent.replaceAll('\n', '<br>');
        if (message != null && message != "")
            messageView = '<small class="text-muted">' + this.convertLinkToClickable(message) + '</small>';

        return messageView;
    }

    /**
     * Get image message view
     *
     * @param {object} messageContent
     * @returns {string|null}
     */
    getMsgImageView(imagUrl) {

        var messageView = null;

        if (imagUrl != null && imagUrl != "") {

            messageView = '<img class="chat-image" ';
            messageView += ' src="' + imagUrl + '"';
            messageView += ' onclick="' + this.classHandlerVar + '.zoomImage(this);" ';
            messageView += ' >';
        }

        return messageView;
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

            /* App\Enums\Chatbot\Messenger\ChatbotMessageTypesEnum: Text|Image */
            var messageViewKey = "MessageTemplate_MessageView";
            var messageTimeKey = "MessageTemplate_MessageTime";

            var isUserMessageKey = "MessageTemplate_isUserMessage";
            var messageTypeKey = "MessageTemplate_MessageType";

            var style1Key = "MessageTemplate_Style1";

            var isUserMessage = data.userId == this.userId;
            var messageType = data.messageType;
            var messageView = data.messageView;
            var messageTime = data.messageTime;

            template_innerHTML = template_innerHTML.replaceAll(isUserMessageKey, isUserMessage);
            template_innerHTML = template_innerHTML.replaceAll(messageTypeKey, messageType);

            template_innerHTML = template_innerHTML.replaceAll(style1Key, isUserMessage ? "ms-5" : "me-5 flex-row-reverse");

            template_innerHTML = template_innerHTML.replaceAll(messageViewKey, messageView);
            template_innerHTML = template_innerHTML.replaceAll(messageTimeKey, messageTime);

        }
        else if (template.id == "ReplierProfileTemplate" || template.id == "UserProfileTemplate") {

            var viewId = data.viewId;

            var sectionId = viewId + '_section';
            var imageId = viewId + '_image';
            var displayNameId = viewId + '_name';

            var resolved = (profileData) => {

                this.displayView(sectionId, true);
                var profileImageView = this.getView(imageId);
                var profileNameView = this.getView(displayNameId);

                profileImageView.src = profileData.profileImage;
                profileNameView.innerHTML = profileData.dispalyName;

                this.displayView(sectionId, true);
            };

            var rejected = () => { };

            this.getProfileData(data.userId).then(resolved, rejected);

            var profileSectionIdKey = "ProfileTemplate_SectionId";
            var profileImageIdKey = "ProfileTemplate_ImageId";
            var profileNameIdKey = "ProfileTemplate_NameId";

            template_innerHTML = template_innerHTML.replaceAll(profileSectionIdKey, sectionId);
            template_innerHTML = template_innerHTML.replaceAll(profileImageIdKey, imageId);
            template_innerHTML = template_innerHTML.replaceAll(profileNameIdKey, displayNameId);
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
            this.displayWaiting(true);

        var serverConnection = new ServerConnection(this.apiBaseUrl);
        serverConnection.setSubUrl('tickets/messenger/' + action);
        serverConnection.setMethod("POST");
        serverConnection.appendHeader("locale", locale);
        serverConnection.appendData("payload", this.payload);

        serverConnection.setOnFailed(function (xhr, status, errorThrown) {
            var response = JSON.parse(xhr.responseText);

            var data = response.data;

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

            if (!this.isEmpty(data)) {

                if (data.refreshPage === true)
                    window.location.reload();

                this.applyDefaultData(data);
            }


            this.log(errorMessage, false, 'warning');


            this.displayWaiting(false);

            /* ignore showin exception errors */
            if (!errorMessage.includes('exception'))
                this.addLocalMessageView(errorMessage);

            if (onFailedCallbackFunc != null)
                onFailedCallbackFunc(response);

        }.bind(this));

        return serverConnection;

    }

    /**
     * Add received messages to chat
     *
     * @param {object} messages
     */
    addReceivedMessages(messages) {

        if (!this.isEmpty(messages)) {

            messages.forEach(message => {

                var messageView = this.getView(message.id);

                /* Avoid adding repeated messages to chat */
                if (this.isEmpty(messageView))
                    this.addMessageView(message);
            });
        }
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

        /* Send new message section */
        if (!this.isEmpty(data.canSendMessage)) {
            this.displayView('new_message_section', this.booleanValue(data.canSendMessage));
        }

        /* Last message id */
        if (!this.isEmpty(data.lastMessageId))
            this.lastMessageId = data.lastMessageId;

        /* Last message id */
        if (!this.isEmpty(data.synchronize))
            this.synchronize = this.booleanValue(data.synchronize);

    }

    /**
     * Get initial data
     */
    getInitialData() {

        var serverConnection = this.getServerConnection('get_initial_data');

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            this.log('getInitialData:\n' + responseJson);

            var data = response.data;

            this.displayWaiting(false);

            this.userId = data.userId;
            this.debugMode = this.booleanValue(data.debugMode);
            this.profiles = data.usersProfiles;

            this.ticketImagePath = data.imagesBasePath.ticketImage;
            this.chatbotUserInputImagePath = data.imagesBasePath.chatbotUserInputImage;

            this.applyDefaultData(data);

            this.getPreviousMessages(true);

            var syncData = setInterval(() => {

                if (this.synchronize)
                    this.getPreviousMessages();
                else
                    clearInterval(syncData);
            }, this.fetchMessageDelay);


        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Get user profile data
     * @param {int} messageData
     */
    getProfileData(userId) {

        return new Promise((resolve, reject) => {

            try {

                if (this.profiles != null) {

                    var profiles = this.profiles;

                    if (!this.isEmpty(profiles[userId])) {

                        var userProfile = profiles[userId];

                        if (!this.isEmpty(userProfile.dispalyName) && !this.isEmpty(userProfile.profileImage)) {

                            resolve(userProfile);
                            return;
                        }
                    }
                }

            } catch (error) {
                this.log('Profile data fetch error. \n Exception: ' + error, true, 'error');
            }

            var onFailedCallbackFunc = (response) => {
                reject(response);
            };

            var serverConnection = this.getServerConnection('get_profile_data', onFailedCallbackFunc);
            serverConnection.appendData('userId', userId);

            serverConnection.setOnSuccess(function (responseJson) {
                var response = JSON.parse(responseJson);

                this.log('getProfileData:\n' + responseJson);

                var data = response.data;

                this.displayWaiting(false);

                this.profiles[userId] = {

                    dispalyName: data.dispalyName,
                    profileImage: data.profileImage,
                };

                this.applyDefaultData(data);
                this.scrolldown();

                resolve(this.profiles[userId]);


            }.bind(this));

            serverConnection.connect();

        });


    }

    /**
     * Get previous messages
     *
     * @param {boolean} fetchAllMessages
     */
    getPreviousMessages(fetchAllMessages = false) {

        var serverConnection = this.getServerConnection('get_previous_messages', null, fetchAllMessages);
        serverConnection.appendData('lastMessageId', fetchAllMessages ? null : this.lastMessageId);

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            this.log('getPreviousMessages:\n' + responseJson);

            var data = response.data;

            this.displayWaiting(false);

            this.addReceivedMessages(data.messages);

            this.applyDefaultData(data);

        }.bind(this));

        serverConnection.connect();
    }

    /**
    * Send message
    */
    sendMessage(inputView) {

        var inputViewTagName = inputView.tagName.toLowerCase();

        var serverConnection = this.getServerConnection('new_message');
        serverConnection.appendData('lastMessageId', this.lastMessageId);

        if (inputViewTagName == "input" && inputView.type == "file") {
            /* files */
            var file = inputView.files[0];
            serverConnection.appendData('attachedFile', file);

        } else {
            serverConnection.appendData('message', inputView.value);
        }

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            this.log('sendMessage:\n' + responseJson);

            inputView.value = "";

            var data = response.data;

            this.displayWaiting(false);

            this.addReceivedMessages(data.messages);

            this.applyDefaultData(data);

        }.bind(this));

        serverConnection.connect();
    }

    /**
    * Change ticket status
    * This is working just for personnel
    */
    changeTicketStatus(inputView) {

        var serverConnection = this.getServerConnection('change_ticket_status', null, false);
        serverConnection.appendData('status', inputView.value);

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            this.addLocalMessageView(response.message);

            var data = response.data;
            var refreshPage = data.refreshPage;

            if (refreshPage)
                window.location.reload();

            this.applyDefaultData(data);

        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Submit sidebar form data
     */
    submitForm(){

        var serverConnection = this.getServerConnection('submit_form', null, false);
        serverConnection.appendData('privateNote', func_getView('private_note').value);

        serverConnection.setOnSuccess(function (responseJson) {
            var response = JSON.parse(responseJson);

            this.addLocalMessageView(response.message);

            var data = response.data;
            var refreshPage = data.refreshPage;

            if (refreshPage)
                window.location.reload();

            this.applyDefaultData(data);

        }.bind(this));

        serverConnection.connect();
    }
}
