class ModalBox {
    /*
     * ModalBox super class
    */

    constructor(modalId) {

        this.modalId = modalId;

        this.modal = this.makeModalWindow();

        // Defaults
        this.width = 0;
        this.onClose = null;
        this.onYesPressed = null;
        this.onNoPressed = null;
        this.setHeader('');
        this.setBody('');

        this.headerElement = document.getElementById("modal_header_" + this.modalId);
        this.bodyElement = document.getElementById("modal_body_" + this.modalId);
        this.footerElement = document.getElementById("modal_footer_" + this.modalId);

        this.btnCustom = document.getElementById("btn_custom_" + this.modalId);
        this.btnRealized = document.getElementById("btn_realized_" + this.modalId);
        this.btnYes = document.getElementById("btn_yes_" + this.modalId);
        this.btnNo = document.getElementById("btn_no_" + this.modalId);

        /*
         By default in Super Class all buttons are hidden
         and will be visible in derivative classes as needed
        */
        this.hideElement(this.btnCustom);
        this.hideElement(this.btnRealized);
        this.hideElement(this.btnYes);
        this.hideElement(this.btnNo);
    }

    create() {

        if (this.modal) {

            this.updateTranslations();
            this.setupDialogWith();
            this.setupDialogButtons();

            this.headerElement.innerHTML = this.getHeader();
            this.bodyElement.innerHTML = this.getBody();

            if (this.getBody() == "exception.CSRF token mismatch.")
                window.location.reload();

            this.showDialog();
        }
    }

    makeModalWindow() {
        var ModalView = document.createElement("div");
        ModalView.innerHTML = this.getModalStructure();
        document.body.appendChild(ModalView);

        return document.getElementById(this.modalId);
    }

    updateTranslations() {
        /**
         * In order for the latest translation changes to take effect,
         * register any items that need to be translated in this section.
         */
        this.btnRealized.value = trans('buttons.iRealized');
        this.btnYes.value = trans('buttons.YES');
        this.btnNo.value = trans('buttons.NO');
    }
    /********************* Internal methods **********************/

    getModalWindow() {
        var modal = document.getElementById('' + this.modalId);

        if (!is_ElementExist(modal)) {

            alert("Error:\n The modal element by id:  \" " + this.modalId + " \" Not Found !! ");
            return false;
        }
        return modal;
    }

    showDialog() {
        this.modal.style.display = "block";
    }

    closeDialog() {
        this.modal.style.display = "none";

        if (this.onClose != null)
            this.onClose();
    }

    btnCustomPressed() {
        if (this.onCustomBtnPressed != null)
            this.onCustomBtnPressed();

        this.closeDialog();
    }

    btnYesPressed() {
        if (this.onYesPressed != null)
            this.onYesPressed();

        this.closeDialog();
    }

    btnNoPressed() {
        if (this.onNoPressed != null)
            this.onNoPressed();

        this.closeDialog();
    }


    setupDialogWith() {
        var width = this.getWidth();
        if (width > 0) {

            var content = document.getElementById("modal_content_" + this.modalId);
            content.style.width = width;
        }
    }

    setupDialogButtons() {

        // When the user clicks on custom button, call function and close the modal
        this.btnCustom.onclick = function () {
            this.btnCustomPressed();
        }.bind(this);

        // When the user clicks on button "YES", call function and close the modal
        var btnYes = document.getElementById("btn_yes_" + this.modalId);
        btnYes.onclick = function () {
            this.btnYesPressed();
        }.bind(this);

        // When the user clicks on button "NO", call function and close the modal
        var btnNo = document.getElementById("btn_no_" + this.modalId);
        btnNo.onclick = function () {
            this.btnNoPressed();
        }.bind(this);

        // When the user wants to close the modal
        this.setupDialogClose();
    }

    setupDialogClose() {

        // When the user clicks on <span> (x), close the modal
        var btnClose = document.getElementById("close_" + this.modalId);
        btnClose.onclick = function () {
            this.closeDialog();
        }.bind(this);

        // When the user clicks "I Realied" buton close the modal
        this.btnRealized.onclick = function () {
            this.closeDialog();
        }.bind(this);

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == this.modal) {
                this.closeDialog();
            }
        }.bind(this);
    }

    hideElement(element) {
        element.style.display = "none";
    }
    showElement(element) {
        element.style.display = "unset";
    }
    /********************* Internal methods END **********************/

    /********************* Setter & Getter **********************/

    setHeader(header) {
        header += ''; // To convert input to string, if not string.
        this.header = header.replaceAll('\n', '<br />');
    }
    getHeader() {
        return this.header;
    }

    setBody(body) {
        body += ''; // To convert input to string, if not string.
        this.body = body.replaceAll('\n', '<br />');
    }
    getBody() {
        return this.body;
    }

    setFooter(footer) {
        this.footer = footer;
    }
    getFooter() {
        return this.footer;
    }

    setWidth(width) {
        if (width != "" && width != null && width > 0)
            this.width = width;
    }
    getWidth() {
        var width = this.width;
        if (width != "" && width != null && width > 0)
            return this.width;

        this.width = 0;

        return 0;
    }

    setCustomBtnLabel(label) {
        this.btnCustom.value = label;
    }

    /********************* Setter & Getter END **********************/

    /********************* Callback Functions **********************/
    /**
     * Fire whenever the dialog closes, including the Cancel, Yes, No buttons.
     * @param {Callbackfunction} onCloseCallback
     */
    setOnClose(onCloseCallback) {
        this.onClose = onCloseCallback;
    }

    /**
     * Fire just when the "Yes" Pressed.
     * @param {Callbackfunction} onCustomBtnPressedCallback
     */
    setOnCustomBtnPressed(onCustomBtnPressedCallback) {
        this.onCustomBtnPressed = onCustomBtnPressedCallback;
    }

    /**
     * Fire just when the "Yes" Pressed.
     * @param {Callbackfunction} onYesPressedCallback
     */
    setOnYesPressed(onYesPressedCallback) {
        this.onYesPressed = onYesPressedCallback;
    }

    /**
     * Fire just when the "No" Pressed.
     * @param {Callbackfunction} onNoPressedCallback
     */
    setOnNoPressed(onNoPressedCallback) {
        this.onNoPressed = onNoPressedCallback;
    }
    /********************* Callback Functions END **********************/

    getModalStructure() {
        var modalId = this.modalId;

        var modalStructure = "";
        modalStructure += "<div id='" + modalId + "' class='hhh_modal' >";
        modalStructure += "<div class='hhh_modal-content' id='modal_content_" + modalId + "' >";

        modalStructure += "<div class='hhh_modal-header'>";
        modalStructure += "<div class='close-hhh_modal' >";
        modalStructure += "<span id='close_" + modalId + "'>&times;</span>";
        modalStructure += "</div>";
        modalStructure += "<h4  id='modal_header_" + modalId + "'></h4>";
        modalStructure += "</div>";

        modalStructure += "<div id='modal_body_" + modalId + "' class='hhh_modal-body'></div>";

        modalStructure += "<div id='modal_footer_" + modalId + "' class='hhh_modal-footer'>";
        modalStructure += "<input id='btn_custom_" + modalId + "' class='btn btn-inverse-dark btn-fw' type='button' value='Custom Button' />";
        modalStructure += "<input id='btn_realized_" + modalId + "' class='btn btn-inverse-dark btn-fw' type='button' value='" + trans('buttons.iRealized') + "' />";
        modalStructure += "<input id='btn_yes_" + modalId + "' class='btn btn-inverse-danger btn-fw' type='button' value='" + trans('buttons.YES') + "' />";
        modalStructure += "<input id='btn_no_" + modalId + "' class='btn btn-inverse-success btn-fw' type='button' value='" + trans('buttons.NO') + "' />";
        modalStructure += "</div>";

        modalStructure += "</div>";
        modalStructure += "</div>";


        return modalStructure;
    }


}
