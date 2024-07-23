class ChatbotCreator {

    constructor(apiBaseUrl, chatbotId) {


        this.apiBaseUrl = apiBaseUrl;
        this.chatbotId = chatbotId;

        this.modalLoading = new Modal_loading();
        this.modalRealize = new ModalRealize('chatbotModalRealize');
        this.modalConfirm = new ModalConfirm('chatbotModalConfirm');

        /* Templates Views */
        this.mainCanvas = this.getView("mainCanvas");
        this.verticalLineTemplate = this.getView("VerticalLineTemplate");
        this.horizantalLineTemplate = this.getView("HorizantalLineTemplate");
        this.addNewStepTemplate = this.getView("AddNewStepTemplate");
        this.horizontalContainerTemplate = this.getView("HorizontalContainerTemplate");

        /* Steps templates views */
        this.moveBotResponseTemplate = this.getView("MoveBotStepTemplate");
        this.editBotResponseTemplate = this.getView("EditBotResponseTemplate");
        this.editBotResponseRandomTextItemTemplate = this.getView("EditBotResponseRandomTextItemTemplate");
        this.editUserInputTemplate = this.getView("EditUserInputTemplate");
        this.editFilterTemplate = this.getView("EditFilterTemplate");
        this.editBotActionTemplate = this.getView("EditBotActionTemplate");

        this.botStepTemplate = this.getView("BotStepTemplate");

        this.refreshChatbotStepsTree();
        this.setupMouseScroller();
    }

    /**
     * Get view template HTML
     *
     * @param {string} viewId
     * @returns string
     */
    getView(viewId) {

        if (is_ElementExist(viewId)) {
            return document.getElementById(viewId);
        }

        alert('No template found with this ID: ' + viewId);
    }

    /**
     * Get server connection handler base on action
     *
     * @param {string} action
     * @returns serverConnection handler
     */
    getServerConnection(action) {

        this.modalLoading.show();

        var serverConnection = new ServerConnection(this.apiBaseUrl);
        serverConnection.setSubUrl('chatbots/creator/' + action);
        serverConnection.setMethod("POST");
        serverConnection.appendHeader("locale", locale);
        serverConnection.appendData("chatbot_id", this.chatbotId);

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

        }.bind(this));

        return serverConnection;

    }

    /**
     * Display step type to splitted words for display
     * [Used in Html]
     *
     * @param {string} str
     * @returns {string}
     */
    stepTypeDisplay(str) {
        return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function (word, index) {
            return index === 0 ? word : word = " " + word;
        });
    }

    /**
     * Set chatbot steps tree
     *
     * @param {JSON} chatbotStepsTree
     */
    setChatbotStepsTree(chatbotStepsTree) {

        this.chatbotStepsTree = JSON.parse(chatbotStepsTree);
    }

    /**
     * Setup mouse events for grabbing and scroller canvas
     */
    setupMouseScroller() {

        var container = document.getElementById('diagramContainer');

        let isDown = false;
        let startX;
        let startY;
        let scrollLeft;
        let scrollTop;

        function mouseDown(event) {
            isDown = true;
            startX = event.pageX - container.offsetLeft;
            startY = event.pageY - container.offsetTop;
            scrollLeft = container.scrollLeft;
            scrollTop = container.scrollTop;
            container.style.cursor = 'grabbing';
        }

        function mouseUp(event) {
            isDown = false;
            container.style.cursor = 'grab';
        }

        function mouseLeave(event) {
            isDown = false;
            container.style.cursor = 'grab';
        }

        function mouseMove(event) {
            if (!isDown) return;
            container.style.cursor = 'grabbing';
            event.preventDefault();
            const x = event.pageX - container.offsetLeft;
            const y = event.pageY - container.offsetTop;
            const walkX = (x - startX) * 1; /* Change this number to adjust the scroll speed */
            const walkY = (y - startY) * 1; /* Change this number to adjust the scroll speed */
            container.scrollLeft = scrollLeft - walkX;
            container.scrollTop = scrollTop - walkY;

        }

        container.addEventListener("mousedown", mouseDown, false);
        container.addEventListener("mouseup", mouseUp, false);
        container.addEventListener("mouseleave", mouseLeave, false);
        container.addEventListener("mousemove", mouseMove, false);
    }

    /**
     * Refresh chatbot steps tree via server
     */
    refreshChatbotStepsTree() {

        var serverConnection = this.getServerConnection('get_steps_tree');

        serverConnection.setOnSuccess(function (response) {
            response = JSON.parse(response);

            var data = response.data;

            this.setChatbotStepsTree(data.chatbotStepsTree);
            this.createChatbotDiagram();

            this.modalLoading.close();
        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Add new step
     *
     * @param {string} chatbotStepType
     * @param {int} parentId
     */
    addNewStep(chatbotStepType, parentId) {

        var serverConnection = this.getServerConnection('add_new_step');
        serverConnection.appendData("type", chatbotStepType);
        serverConnection.appendData("parent_id", parentId);

        serverConnection.setOnSuccess(function (response) {
            response = JSON.parse(response);

            var data = response.data;

            this.setChatbotStepsTree(data.chatbotStepsTree);
            this.createChatbotDiagram();

            this.modalLoading.close();
        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Clear canvas
     */
    clearCanvas() {

        this.mainCanvas.innerHTML = "";
    }

    /**
     * Add template to view
     *
     * @param {string} parentView
     * @param {string} template
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

        if (template.id == "EditBotResponseTemplate") {

            var responseTypeDropdown = document.getElementById("StepResponseType_" + data.id);
            this.setDropdownSelectedItem(responseTypeDropdown.id, data.action.Type);
            this.toggleBotResponseForm(responseTypeDropdown, data.id);

            this.setDynamicDataToEditBotResponse("", data, true);
        }
        else if (template.id == "EditUserInputTemplate") {

            var userInputTypeDropdown = document.getElementById("StepUserInputType_" + data.id);
            this.setDropdownSelectedItem(userInputTypeDropdown.id, data.action.Type);
            this.toggleBotUserInputForm(userInputTypeDropdown, data.id);

            this.setDynamicDataToEditUserInput("", data, true);
        }
        else if (template.id == "EditFilterTemplate") {

            var filterTypeDropdown = document.getElementById("StepFilterType_" + data.id);
            this.setDropdownSelectedItem(filterTypeDropdown.id, data.action.Type);
            this.toggleBotFilterForm(filterTypeDropdown, data.id);

            this.setDynamicDataToEditFilter("", data, true);
        }
        else if (template.id == "EditBotActionTemplate") {

            var filterTypeDropdown = document.getElementById("StepFilterType_" + data.id);
            this.setDropdownSelectedItem(filterTypeDropdown.id, data.action.Type);
            this.toggleBotActionForm(filterTypeDropdown, data.id);

            this.setDynamicDataToEditBotAction("", data, true);
        }
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
            id = Date.now();

        var template_innerHTML = template.innerHTML;


        if (template.id == "AddNewStepTemplate") {

            var dropdownId = "AddNewStepTemplate_dropdownMenuButton";
            var newStepParentID = "AddNewStepTemplate_ParentID";

            template_innerHTML = template_innerHTML.replaceAll(dropdownId, id + "_dropdownMenuButton");
            template_innerHTML = template_innerHTML.replaceAll(newStepParentID, data.parentId);

        }
        else if (template.id == "HorizontalContainerTemplate") {

            var ContainerCanvasId = "HorizontalContainerCanvas";

            template_innerHTML = template_innerHTML.replaceAll(ContainerCanvasId, id + '_canvas');
        }
        else if (template.id == "HorizantalLineTemplate") {

            var directionKey = "HorizantalLineTemplate_Direction";
            var widthKey = "HorizantalLineTemplate_Width";

            var width = '100%';
            if (data.isFirstStep || data.isLastStep)
                width = data.isFirstStep ? 'calc(50% + 0.25rem + 1px)' : 'calc(50% - 0.25rem + 1px)';


            template_innerHTML = template_innerHTML.replaceAll(directionKey, data.isFirstStep ? "flex-end" : "flex-start");
            template_innerHTML = template_innerHTML.replaceAll(widthKey, width);
        }
        else if (template.id == "BotStepTemplate") {

            var stepIdKey = "ChatbotStepTemplate_StepID";
            var stepTypeNameKey = "ChatbotStepTemplate_TranslatedTypeName";
            var stepNameKey = "ChatbotStepTemplate_Name";
            var showDeleteWithChildsKey = "BotStepTemplate_Show_DeleteWithChilds";

            template_innerHTML = template_innerHTML.replaceAll(stepIdKey, data.id);
            template_innerHTML = template_innerHTML.replaceAll(stepTypeNameKey, data.translated_step_type);
            template_innerHTML = template_innerHTML.replaceAll(stepNameKey, data.name == null ? "UNTITLED" : data.name);
            template_innerHTML = template_innerHTML.replaceAll(showDeleteWithChildsKey, (data.childs.length > 0) ? "" : "d-none");

        }
        else if (template.id == "MoveBotStepTemplate") {
            var stepIdKey = "ChatbotStep_Id";
            var stepTypeNameKey = "ChatbotStep_TranslatedTypeName";
            var StepTitleValueKey = "StepTitleValue";

            template_innerHTML = template_innerHTML.replaceAll(stepIdKey, data.id);
            template_innerHTML = template_innerHTML.replaceAll(stepTypeNameKey, data.translated_step_type);
            template_innerHTML = template_innerHTML.replaceAll(StepTitleValueKey, data.name == null ? "" : data.name);
        }
        else if (template.id == "EditBotResponseTemplate") {

            var action = data.action;

            var stepIdKey = "ChatbotStep_Id";
            var stepTypeNameKey = "ChatbotStep_TranslatedTypeName";
            var StepTitleValueKey = "StepTitleValue";
            var StepDelayValueKey = "StepDelayValue";

            template_innerHTML = template_innerHTML.replaceAll(stepIdKey, data.id);
            template_innerHTML = template_innerHTML.replaceAll(stepTypeNameKey, data.translated_step_type);
            template_innerHTML = template_innerHTML.replaceAll(StepTitleValueKey, data.name == null ? "" : data.name);
            template_innerHTML = template_innerHTML.replaceAll(StepDelayValueKey, action.Delay == null ? "" : action.Delay);

            template_innerHTML = this.setDynamicDataToEditBotResponse(template_innerHTML, data, false);
        }
        else if (template.id == "EditBotResponseRandomTextItemTemplate") {

            var stepIdKey = "ChatbotStep_Id";
            var randomtextIndexKey = "RandomtextIndex";
            var randomtextNumberKey = "RandomtextNumber";

            template_innerHTML = template_innerHTML.replaceAll(stepIdKey, data.stepId);
            template_innerHTML = template_innerHTML.replaceAll(randomtextIndexKey, data.index);
            template_innerHTML = template_innerHTML.replaceAll(randomtextNumberKey, data.index + 1);
        }
        else if (template.id == "EditUserInputTemplate") {

            var action = data.action;

            var stepIdKey = "ChatbotStep_Id";
            var stepTypeNameKey = "ChatbotStep_TranslatedTypeName";
            var StepTitleValueKey = "StepTitleValue";

            template_innerHTML = template_innerHTML.replaceAll(stepIdKey, data.id);
            template_innerHTML = template_innerHTML.replaceAll(stepTypeNameKey, data.translated_step_type);
            template_innerHTML = template_innerHTML.replaceAll(StepTitleValueKey, data.name == null ? "" : data.name);

            template_innerHTML = this.setDynamicDataToEditUserInput(template_innerHTML, data, false);
        }
        else if (template.id == "EditFilterTemplate") {

            var action = data.action;

            var stepIdKey = "ChatbotStep_Id";
            var stepTypeNameKey = "ChatbotStep_TranslatedTypeName";
            var StepTitleValueKey = "StepTitleValue";

            template_innerHTML = template_innerHTML.replaceAll(stepIdKey, data.id);
            template_innerHTML = template_innerHTML.replaceAll(stepTypeNameKey, data.translated_step_type);
            template_innerHTML = template_innerHTML.replaceAll(StepTitleValueKey, data.name == null ? "" : data.name);

            template_innerHTML = this.setDynamicDataToEditFilter(template_innerHTML, data, false);
        }
        else if (template.id == "EditBotActionTemplate") {

            var action = data.action;

            var stepIdKey = "ChatbotStep_Id";
            var stepTypeNameKey = "ChatbotStep_TranslatedTypeName";
            var StepTitleValueKey = "StepTitleValue";

            template_innerHTML = template_innerHTML.replaceAll(stepIdKey, data.id);
            template_innerHTML = template_innerHTML.replaceAll(stepTypeNameKey, data.translated_step_type);
            template_innerHTML = template_innerHTML.replaceAll(StepTitleValueKey, data.name == null ? "" : data.name);

            template_innerHTML = this.setDynamicDataToEditBotAction(template_innerHTML, data, false);
        }

        return template_innerHTML;
    }

    /**
     * Set dynamic data for EditBotResponse template
     *
     * @param {string} templateHtml
     * @param {object} data
     * @param {boolean} afterInsertTempalte
     * @returns {string}
     */
    setDynamicDataToEditBotResponse(templateHtml, data, afterInsertTempalte) {

        var stepId = data.id;
        var action = data.action;
        var actionType = action.Type;
        var actionData = action.Data;

        var dynKeys = {
            stepResponseTextValueKey: "StepResponseTextValue",

            stepResponseImage_FileName: "StepResponseImage_FileName",

            stepResponseButton_TitleValue: "StepResponseButton_TitleValue",
            stepResponseButton_TargetStepValue: "StepResponseButton_TargetStepValue",
            stepResponseButton_TargetUrlValue: "StepResponseButton_TargetUrlValue",
        };

        if (actionType == "Text" && !afterInsertTempalte) {

            templateHtml = templateHtml.replaceAll(dynKeys.stepResponseTextValueKey, this.replaceNull(actionData.TextValue));

        } else if (actionType == "RandomText" && afterInsertTempalte) {

            var texts = actionData.Texts;

            texts.forEach(text => {

                this.botRandomTextNewText(stepId);
                document.getElementById('RandomTextStepTextarea_' + stepId).value = text;
                this.botRandomTextInputChanged(stepId);
            });

            if (texts.length > 0) {
                this.botRandomTextBtnClicked(stepId, 0);
            }


        } else if (actionType == "Image") {

            var fileName = actionData.FileName;

            if (afterInsertTempalte) {
                this.displayView("StepResponseImage_DisplayImage_" + stepId, fileName != null);
            } else {
                templateHtml = templateHtml.replaceAll(dynKeys.stepResponseImage_FileName, this.replaceNull(fileName));
            }

        } else if (actionType == "Button") {

            if (afterInsertTempalte) {
                var selectedButtonType = actionData.Type;

                var buttonTypeDropdown = document.getElementById('StepResponseButton_Type_' + stepId);
                if (selectedButtonType != null) {

                    buttonTypeDropdown.value = selectedButtonType;
                }
                this.toggleBotResponseButtonForm(buttonTypeDropdown, stepId);

            } else {
                templateHtml = templateHtml.replaceAll(dynKeys.stepResponseButton_TitleValue, this.replaceNull(actionData.Title));
                templateHtml = templateHtml.replaceAll(dynKeys.stepResponseButton_TargetStepValue, this.replaceNull(actionData.TargetStep));
                templateHtml = templateHtml.replaceAll(dynKeys.stepResponseButton_TargetUrlValue, this.replaceNull(actionData.TargetUrl));
            }
        }

        /* Clear unused keys */
        Object.entries(dynKeys).forEach(entry => {
            const [key, value] = entry;
            templateHtml = templateHtml.replaceAll(value, "");
        });

        return templateHtml;
    }

    /**
     * Set dynamic data for EditUserInput template
     *
     * @param {string} templateHtml
     * @param {object} data
     * @param {boolean} afterInsertTempalte
     * @returns {string}
     */
    setDynamicDataToEditUserInput(templateHtml, data, afterInsertTempalte) {

        var stepId = data.id;
        var action = data.action;
        var actionType = action.Type;
        var actionData = action.Data;

        var dynKeys = {
            stepUserInputNumberTitleValueKey: "StepUserInputNumberTitleValue",
            stepUserInputNumberDescriptionValueKey: "StepUserInputNumberDescriptionValue",
            stepUserInputNumberPlaceholderValueKey: "StepUserInputNumberPlaceholderValue",
            stepUserInputNumberRequiredCheckedKey: "step_user_input_number_required_checked",
            stepUserInputNumberMinValueKey: "StepUserInputNumberMinValue",
            stepUserInputNumberMaxValueKey: "StepUserInputNumberMaxValue",

            stepUserInputOneLineTextTitleValueKey: "StepUserInputOneLineTextTitleValue",
            stepUserInputOneLineTextDescriptionValueKey: "StepUserInputOneLineTextDescriptionValue",
            stepUserInputOneLineTextPlaceholderValueKey: "StepUserInputOneLineTextPlaceholderValue",
            stepUserInputOneLineTextRequiredCheckedKey: "step_user_input_olt_required_checked",
            stepUserInputOneLineTextMinValueKey: "StepUserInputOneLineTextMinValue",
            stepUserInputOneLineTextMaxValueKey: "StepUserInputOneLineTextMaxValue",

            stepUserInputMultipleLineTextTitleValueKey: "StepUserInputMultipleLineTextTitleValue",
            stepUserInputMultipleLineTextDescriptionValueKey: "StepUserInputMultipleLineTextDescriptionValue",
            stepUserInputMultipleLineTextPlaceholderValueKey: "StepUserInputMultipleLineTextPlaceholderValue",
            stepUserInputMultipleLineTextRequiredCheckedKey: "step_user_input_mlt_required_checked",
            stepUserInputMultipleLineTextMinValueKey: "StepUserInputMultipleLineTextMinValue",
            stepUserInputMultipleLineTextMaxValueKey: "StepUserInputMultipleLineTextMaxValue",

            stepUserInputImageTitleValueKey: "StepUserInputImageTitleValue",
            stepUserInputImageDescriptionValueKey: "StepUserInputImageDescriptionValue",
            stepUserInputImageRequiredCheckedKey: "step_user_input_img_required_checked",

        };

        if (actionType == "Number" && !afterInsertTempalte) {

            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputNumberTitleValueKey, this.replaceNull(actionData.Title_Num));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputNumberDescriptionValueKey, this.replaceNull(actionData.Description_Num));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputNumberPlaceholderValueKey, this.replaceNull(actionData.Placeholder_Num));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputNumberRequiredCheckedKey, this.booleanValue(actionData.Required_Num) ? 'checked' : '');
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputNumberMinValueKey, this.replaceNull(actionData.Min_Num));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputNumberMaxValueKey, this.replaceNull(actionData.Max_Num));
        }
        else if (actionType == "OneLineText" && !afterInsertTempalte) {

            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputOneLineTextTitleValueKey, this.replaceNull(actionData.Title_OLT));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputOneLineTextDescriptionValueKey, this.replaceNull(actionData.Description_OLT));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputOneLineTextPlaceholderValueKey, this.replaceNull(actionData.Placeholder_OLT));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputOneLineTextRequiredCheckedKey, this.booleanValue(actionData.Required_OLT) ? 'checked' : '');
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputOneLineTextMinValueKey, this.replaceNull(actionData.MinLenght_OLT));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputOneLineTextMaxValueKey, this.replaceNull(actionData.MaxLenght_OLT));
        }
        else if (actionType == "MultipleLineText" && !afterInsertTempalte) {

            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputMultipleLineTextTitleValueKey, this.replaceNull(actionData.Title_MLT));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputMultipleLineTextDescriptionValueKey, this.replaceNull(actionData.Description_MLT));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputMultipleLineTextPlaceholderValueKey, this.replaceNull(actionData.Placeholder_MLT));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputMultipleLineTextRequiredCheckedKey, this.booleanValue(actionData.Required_MLT) ? 'checked' : '');
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputMultipleLineTextMinValueKey, this.replaceNull(actionData.MinLenght_MLT));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputMultipleLineTextMaxValueKey, this.replaceNull(actionData.MaxLenght_MLT));
        }
        else if (actionType == "Image" && !afterInsertTempalte) {

            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputImageTitleValueKey, this.replaceNull(actionData.Title_Img));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputImageDescriptionValueKey, this.replaceNull(actionData.Description_Img));
            templateHtml = templateHtml.replaceAll(dynKeys.stepUserInputImageRequiredCheckedKey, this.booleanValue(actionData.Required_Img) ? 'checked' : '');
        }

        /* Clear unused keys */
        Object.entries(dynKeys).forEach(entry => {
            const [key, value] = entry;
            templateHtml = templateHtml.replaceAll(value, "");
        });

        return templateHtml;
    }

    /**
     * Set dynamic data for Filter template
     *
     * @param {string} templateHtml
     * @param {object} data
     * @param {boolean} afterInsertTempalte
     * @returns {string}
     */
    setDynamicDataToEditFilter(templateHtml, data, afterInsertTempalte) {

        var stepId = data.id;
        var action = data.action;
        var actionType = action.Type;
        var actionData = action.Data;

        var dynKeys = {
            /* stepUserInputNumberTitleValueKey: "StepUserInputNumberTitleValue", */
        };

        if (actionType == "ClientCategory" && afterInsertTempalte) {

            var allowedCategories = actionData.AllowedCategories;

            allowedCategories.forEach(allowedCategory => {

                var checkboxObj = document.getElementById('FilterClientCategory_' + stepId + '_Checkbox_' + allowedCategory);
                checkboxObj.checked = true;
                checkboxObj.setAttribute('checked', true);
            });
        }


        /* Clear unused keys */
        Object.entries(dynKeys).forEach(entry => {
            const [key, value] = entry;
            templateHtml = templateHtml.replaceAll(value, "");
        });

        return templateHtml;
    }

    /**
     * Set dynamic data for Filter template
     *
     * @param {string} templateHtml
     * @param {object} data
     * @param {boolean} afterInsertTempalte
     * @returns {string}
     */
    setDynamicDataToEditBotAction(templateHtml, data, afterInsertTempalte) {

        var stepId = data.id;
        var action = data.action;
        var actionType = action.Type;
        var actionData = action.Data;

        var dynKeys = {
            botActionGoToStep_TargetStepValueKey: "BotActionGoToStep_TargetStepValue",
            botActionStartTicket_TicketSubjectValueKey: "BotActionStartTicket_TicketSubjectValue",
            botActionStartTicket_HourLimitValueKey: "BotActionStartTicket_HourLimitValue",
            botActionStartTicket_NumberLimitValueKey: "BotActionStartTicket_NumberLimitValue",
            botActionStartTicket_ScheduleFaildTargetStepValue: "BotActionStartTicket_ScheduleFaildTargetStepValue",
        };

        if (actionType == "GoToStep" && !afterInsertTempalte) {

            templateHtml = templateHtml.replaceAll(dynKeys.botActionGoToStep_TargetStepValueKey, this.replaceNull(actionData.TargetStep));
        }
        else if (actionType == "StartTicket" && !afterInsertTempalte) {

            templateHtml = templateHtml.replaceAll(dynKeys.botActionStartTicket_TicketSubjectValueKey, this.replaceNull(actionData.TicketSubject));
            templateHtml = templateHtml.replaceAll(dynKeys.botActionStartTicket_HourLimitValueKey, this.replaceNull(actionData.HourLimit));
            templateHtml = templateHtml.replaceAll(dynKeys.botActionStartTicket_NumberLimitValueKey, this.replaceNull(actionData.NumberLimit));
            templateHtml = templateHtml.replaceAll(dynKeys.botActionStartTicket_ScheduleFaildTargetStepValue, this.replaceNull(actionData.ScheduleFaildTargetStep));
        }
        else if (actionType == "MakeTicket" && afterInsertTempalte) {
            this.toggleBotActionPriority(stepId, actionData.TicketPriority);
        }


        /* Clear unused keys */
        Object.entries(dynKeys).forEach(entry => {
            const [key, value] = entry;
            templateHtml = templateHtml.replaceAll(value, "");
        });

        return templateHtml;
    }


    /**
     * replace Null with empty text
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
     *
     * @param {string} dropdownId
     * @param {string} selectedKey
     * @returns void
     */
    setDropdownSelectedItem(dropdownId, selectedKey) {

        var dropdownObj = document.getElementById(dropdownId);
        var option;

        for (var i = 0; i < dropdownObj.options.length; i++) {
            option = dropdownObj.options[i];

            if (option.value == selectedKey) {
                option.setAttribute('selected', true);
                return;
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

        var element = document.getElementById(id);

        if (is_ElementExist(element)) {

            var hideClass = 'd-none';
            var isHidden = func_hasClass(element, hideClass);

            if (show && isHidden)
                toggleElementClass(id, hideClass);
            else if (!show && !isHidden)
                toggleElementClass(id, hideClass);
        }
    }

    /**
     * Create chatbot diagram
     *
     * @param {number} parentId
     * @param {object} stepsTree
     * @param {object} canvas
     */
    createChatbotDiagram(parentId = 0, stepsTree = null, canvas = null) {

        if (stepsTree == null)
            stepsTree = this.chatbotStepsTree;

        if (canvas == null)
            canvas = this.mainCanvas;

        if (parentId == 0) {
            this.clearCanvas();

            /* Start point new item */
            this.addTemplateToView(canvas, this.verticalLineTemplate, null);
            this.addTemplateToView(canvas, this.addNewStepTemplate, "AddNewStep_" + parentId, { parentId: parentId });
        }


        if (stepsTree.length == 1) {
            this.addTemplateToView(canvas, this.verticalLineTemplate, null);
        } else if (stepsTree.length > 1) {
            this.addTemplateToView(canvas, this.verticalLineTemplate, null);
            this.addTemplateToView(canvas, this.horizontalContainerTemplate, "horizontalContainer_par_" + parentId);
            canvas = document.getElementById("horizontalContainer_par_" + parentId + "_canvas");

        }


        stepsTree.forEach((step, key, arr) => {

            var isFirstStep = key === 0;
            var isLastStep = key === arr.length - 1;

            var containerId = 'container_' + step.id;
            var containerHtml = '<div id="' + containerId + (isLastStep ? '"></div>' : '" class="pe-2"></div>');

            canvas.innerHTML += containerHtml;

            var container = document.getElementById(containerId);

            if (arr.length > 1) {

                this.addTemplateToView(container, this.horizantalLineTemplate, null, { isFirstStep: isFirstStep, isLastStep: isLastStep });
                this.addTemplateToView(container, this.verticalLineTemplate, null);
            }
            this.addTemplateToView(container, this.botStepTemplate, 'viewStep_' + step.id, step);

            /* Add step move form */
            var moveStepView = document.getElementById('moveStepContainer_' + step.id);
            this.addTemplateToView(moveStepView, this.moveBotResponseTemplate, 'moveStepSection_' + step.id, step);

            /* Add step edit form */
            var stepEditView = document.getElementById('editStepContainer_' + step.id);
            this.addTemplateToView(stepEditView, this.getStepEditTemplate(step.type), 'editStepSection_' + step.id, step);

            /* Step new item */
            var isFinalStep = (step.type == "BotAction") ? step.action.Data.IsFinalStep : false;
            if (!isFinalStep) {
                this.addTemplateToView(container, this.verticalLineTemplate, null);
                this.addTemplateToView(container, this.addNewStepTemplate, "AddNewStep_" + step.id, { parentId: step.id });
            }

            if (step.childs.length > 0) {
                var childsContainerId = 'stepChilds_' + step.id;
                var childsContainerHtml = '<div id="' + childsContainerId + '"></div>';
                container.innerHTML += childsContainerHtml;
                var childsCanvas = document.getElementById(childsContainerId);
                this.createChatbotDiagram(step.id, step.childs, childsCanvas);
            }
        });

    }

    /**
     * Get step template bas on step type
     * These items come from \App\Enums\Chatbot\ChatbotStepTypesEnum
     *
     * @param {string} type
     * @returns {string}
     */
    getStepEditTemplate(type) {

        if (type == "BotResponse")
            return this.editBotResponseTemplate;
        else if (type == "UserInput")
            return this.editUserInputTemplate;
        else if (type == "Filter")
            return this.editFilterTemplate;
        else if (type == "BotAction")
            return this.editBotActionTemplate;

        alert("Error: Edit template for step type \"" + type + "\" not defined.");
        return "";
    }

    /**
     * Delete step
     *
     * @param {int} stepId
     * @param {string} stepName
     * @param {boolean} deleteChilds
     */
    deleteStep(stepId, stepName, deleteChilds = false) {

        var modalConfirm = this.modalConfirm;
        var confirmMessage = document.getElementById('ChatbotConfirm_DeleteStep').value;
        confirmMessage = confirmMessage.replace('ChatbotDeleteConfirm_StepTitle', '"' + stepName + '"');
        modalConfirm.setHeader(trans('alert.Delete'));
        modalConfirm.setBody(confirmMessage);
        modalConfirm.create();

        modalConfirm.setOnYesPressed(function () {

            var serverConnection = this.getServerConnection('delete_step');
            serverConnection.appendData("stepId", stepId);
            serverConnection.appendData("deleteChilds", deleteChilds);

            serverConnection.setOnSuccess(function (response) {
                response = JSON.parse(response);

                var data = response.data;

                this.setChatbotStepsTree(data.chatbotStepsTree);
                this.createChatbotDiagram();

                this.modalLoading.close();
            }.bind(this));

            serverConnection.connect();

        }.bind(this));

    }

    /**
     * Move step dialog
     *
     * @param {int} stepId
     */
    moveStepDialog(stepId) {

        /* Hide last open edit view */
        this.displayView(this.openDialogFormId, false);

        /* Show step edit view */
        var moveStepContainerId = 'moveStepContainer_' + stepId;
        this.displayView(moveStepContainerId, true);
        this.openDialogFormId = moveStepContainerId;
    }

    /**
     * Move step to new position
     *
     * @param {int} stepId
     */
    moveStep(stepId) {

        var form = document.getElementById('StepMoveForm_' + stepId);

        var serverConnection = this.getServerConnection('move_step');
        serverConnection.appendData("stepId", stepId);

        /* Append form inputs data */
        for (var i = 0; i < form.elements.length; i++) {
            var input = form.elements[i];

            serverConnection.appendData(input.name, input.value);
        }

        serverConnection.setOnSuccess(function (response) {
            response = JSON.parse(response);

            var data = response.data;

            this.setChatbotStepsTree(data.chatbotStepsTree);
            this.createChatbotDiagram();

            this.modalLoading.close();
            this.displayView(this.openDialogFormId, true);
        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Edit step
     *
     * @param {int} stepId
     */
    editStep(stepId) {

        /* Hide last open edit view */
        this.displayView(this.openDialogFormId, false);

        /* Show step edit view */
        var editStepContainerId = 'editStepContainer_' + stepId;
        this.displayView(editStepContainerId, true);
        this.openDialogFormId = editStepContainerId;
    }

    /**
     * Update step
     *
     * @param {int} stepId
     */
    updateStep(stepId) {

        var form = document.getElementById('StepForm_' + stepId);

        var serverConnection = this.getServerConnection('update_step');
        serverConnection.appendData("stepId", stepId);

        /* Append form inputs data */
        for (var i = 0; i < form.elements.length; i++) {
            var input = form.elements[i];

            if (input.type == "file") {
                if (input.value != null && input.value != "")
                    serverConnection.appendData(input.name, input.files[0]);
            }
            else if (input.type == "checkbox") {
                serverConnection.appendData(input.name, input.checked);
            }
            else
                serverConnection.appendData(input.name, input.value);
        }

        serverConnection.setOnSuccess(function (response) {
            response = JSON.parse(response);

            var data = response.data;

            this.setChatbotStepsTree(data.chatbotStepsTree);
            this.createChatbotDiagram();

            this.modalLoading.close();
            this.displayView(this.openDialogFormId, true);
        }.bind(this));

        serverConnection.connect();
    }

    /**
     * Toggle chatbot step form base on type
     *
     * @param {object} dropdownObj
     * @param {string} stepId
     * @param {string} formIdPrefix
     */
    toggleBotStepTypeForm(dropdownObj, stepId, formIdPrefix) {

        var selectedItem = dropdownObj.value;
        var option;

        for (var i = 0; i < dropdownObj.options.length; i++) {
            option = dropdownObj.options[i];

            var formId = formIdPrefix + option.value + '_' + stepId;

            this.displayView(formId, option.value == selectedItem);
        }
    }

    /**
     * Toggle chatbot response form base on response type
     *
     * @param {object} dropdownObj
     * @param {string} stepId
     */
    toggleBotResponseForm(dropdownObj, stepId) {

        this.toggleBotStepTypeForm(dropdownObj, stepId, 'ResponseTypeForm_');

        var botResponseButtonDropdown = document.getElementById('StepResponseButton_Type_' + stepId);
        this.toggleBotResponseButtonForm(botResponseButtonDropdown, stepId);
    }

    /**
     * Toggle chatbot UserInput form base on UserInput type
     *
     * @param {object} dropdownObj
     * @param {string} stepId
     */
    toggleBotUserInputForm(dropdownObj, stepId) {

        this.toggleBotStepTypeForm(dropdownObj, stepId, 'UserInputTypeForm_');
    }

    /**
     * Toggle chatbot BotFilter form base on Filter type
     *
     * @param {object} dropdownObj
     * @param {string} stepId
     */
    toggleBotFilterForm(dropdownObj, stepId) {

        this.toggleBotStepTypeForm(dropdownObj, stepId, 'FilterTypeForm_');
    }

    /**
     * Toggle chatbot BotAction form base on action type
     *
     * @param {object} dropdownObj
     * @param {string} stepId
     */
    toggleBotActionForm(dropdownObj, stepId) {

        this.toggleBotStepTypeForm(dropdownObj, stepId, 'BotActionTypeForm_');
    }

    /**
     * Adding new text to RandomText
     *
     * @param {int} stepId
     */
    botRandomTextNewText(stepId) {

        var counterContainer = document.getElementById("RandomTextCounterContainer_" + stepId);

        var texts = counterContainer.querySelectorAll('*[name*="RandomTextItemSection"]');

        var randomtextIndex = texts.length;

        var id = this.getRandomTextDivId(stepId, randomtextIndex);
        this.addTemplateToView(counterContainer, this.editBotResponseRandomTextItemTemplate, id, { stepId: stepId, index: randomtextIndex });

        this.botRandomTextBtnClicked(stepId, randomtextIndex);
    }

    /**
     * Get RandomText Div ID
     *
     * @param {int} stepId
     * @param {int} randomtextIndex
     * @returns {string}
     */
    getRandomTextDivId(stepId, randomtextIndex) {

        return 'RandomText_step_' + stepId + '_index_' + randomtextIndex;
    }

    /**
     *
     * @param {int} stepId
     * @param {int} randomtextIndex
     */
    botRandomTextBtnClicked(stepId, randomtextIndex) {

        var divId = this.getRandomTextDivId(stepId, randomtextIndex);
        var stepContainer = document.getElementById('RandomTextCounterContainer_' + stepId);
        var stepTextInputSection = document.getElementById('RandomTextInputSection_' + stepId);
        var stepTextarea = document.getElementById('RandomTextStepTextarea_' + stepId);
        var btnDeleletItem = document.getElementById('RandomTextBtnDelItem_' + stepId);
        var itemTextarea = document.getElementById('RandomTextItemTextarea_' + stepId + '_' + randomtextIndex);

        /* Show text input section */
        this.displayView(stepTextInputSection.id, true);

        btnDeleletItem.dataset.targetId = this.getRandomTextDivId(stepId, randomtextIndex);
        stepTextarea.dataset.targetId = itemTextarea.id;
        stepTextarea.value = itemTextarea.innerHTML;
        stepTextarea.innerHTML = itemTextarea.innerHTML;

        var btnId = 'RandomTextItemBtn_' + stepId + '_' + randomtextIndex;

        var buttons = stepContainer.querySelectorAll('*[name*="RandomTextItemBtn"]');

        var clickedBtnClass = 'btn-success';
        var unclickedBtnClass = 'btn-primary';
        buttons.forEach(element => {

            if (element.id == btnId) {

                if (func_hasClass(element, unclickedBtnClass))
                    toggleElementClass(element.id, unclickedBtnClass);

                if (!func_hasClass(element, clickedBtnClass))
                    toggleElementClass(element.id, clickedBtnClass);
            } else {
                if (func_hasClass(element, clickedBtnClass))
                    toggleElementClass(element.id, clickedBtnClass);

                if (!func_hasClass(element, unclickedBtnClass))
                    toggleElementClass(element.id, unclickedBtnClass);
            }
        });

    }

    /**
     * The main input textarea changed
     *
     * @param {int} stepId
     */
    botRandomTextInputChanged(stepId) {


        var stepTextarea = document.getElementById('RandomTextStepTextarea_' + stepId);
        var targetTextareaId = stepTextarea.dataset.targetId;
        var itemTextarea = document.getElementById(targetTextareaId);

        itemTextarea.innerHTML = stepTextarea.value;
    }

    /**
     * Delete RandomText item
     *
     * @param {int} stepId
     * @param {element} btn
     */
    deleteRandomText(stepId, btn) {

        var targetId = btn.dataset.targetId;
        var targetElement = document.getElementById(targetId);
        targetElement.parentNode.removeChild(targetElement);

        var stepContainer = document.getElementById('RandomTextCounterContainer_' + stepId);
        var stepTextInputSection = document.getElementById('RandomTextInputSection_' + stepId);
        var buttons = stepContainer.querySelectorAll('*[name*="RandomTextItemBtn"]');

        buttons.forEach((element, index, arr) => {

            element.innerHTML = index + 1;
        });

        if (buttons.length > 0)
            buttons[0].click();
        else
            this.displayView(stepTextInputSection.id, false);

    }

    /**
     * Chatbot response image: upload Button clicked
     *
     * @param {int} stepId
     */
    botResponseImageUploadBtnClicked(stepId) {
        var hiddenUploadBtn = document.getElementById('ResponseImageFormImageUpload_' + stepId);

        hiddenUploadBtn.click();
    }

    /**
     * Chatbot response image: new file selected
     *
     * @param {int} stepId
     */
    botResponseImageNewFileSelected(stepId) {

        var hiddenUploadBtn = document.getElementById('ResponseImageFormImageUpload_' + stepId);
        var selectedFileNameInput = document.getElementById('ResponseImageFormImageUploadName_' + stepId);

        var fakePath = hiddenUploadBtn.value;

        try {
            selectedFileNameInput.value = fakePath.split("\\").pop();
        } catch (error) {
            selectedFileNameInput.value = fakePath;
        } finally {

        }

    }

    /**
     * Toggle chatbot response button form base on button type
     *
     * @param {object} dropdownObj
     * @param {string} stepId
     */
    toggleBotResponseButtonForm(dropdownObj, stepId) {

        var selectedItem = dropdownObj.value;
        var option;

        for (var i = 0; i < dropdownObj.options.length; i++) {
            option = dropdownObj.options[i];

            var isSelectedOption = option.value == selectedItem;

            if (isSelectedOption)
                option.setAttribute('selected', true);
            else
                option.removeAttribute('selected');

            var formId = 'StepResponseButton_TypeSection_' + option.value + '_' + stepId;
            this.displayView(formId, isSelectedOption);
        }
    }

    /**
     * Toggle chatbot action priority base on selected item
     *
     * @param {string} stepId
     */
    toggleBotActionPriority(stepId, selectedItem) {

        if (stepId == null) return;
        if (selectedItem == null)
            selectedItem = "Normal";

        var dropdownObj = document.getElementById('BotActionMakeTicket_Priority_' + stepId);

        var option;

        for (var i = 0; i < dropdownObj.options.length; i++) {
            option = dropdownObj.options[i];

            var isSelectedOption = option.value == selectedItem;

            if (isSelectedOption)
                option.setAttribute('selected', true);
            else
                option.removeAttribute('selected');
        }

        dropdownObj.value = selectedItem;
        dropdownObj.setAttribute('value', selectedItem);
    }

}
