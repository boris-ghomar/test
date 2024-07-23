class Modal_loading {

    constructor() {

        this.modalId = "modalBoxLoading";
        this.view = "view_JumpingDotsLoader";

        this.modal = this.makeModalWindow();
    }


    makeModalWindow() {
        var ModalView = document.createElement("div");
        ModalView.innerHTML = this.getModalStructure();
        document.body.appendChild(ModalView);

        return document.getElementById(this.modalId);
    }

    /********************* Internal methods **********************/

    show() {
        this.modal.style.display = "unset";
    }

    close() {
        this.modal.style.display = "none";
    }
    /********************* Internal methods END **********************/

    /********************* View Structures  **********************/
    getModalStructure() {

        var viewFunc = eval("this." + this.view + "()");

        return viewFunc;
    }

    view_JumpingDotsLoader() {

        var modalId = this.modalId;

        var modalStructure = "";

        modalStructure += "<div id='" + modalId + "' class='hhh_modal_loding' >";

        modalStructure += "<div class='jumping-dots-loader'>";
        modalStructure += "<span></span>";
        modalStructure += "<span></span>";
        modalStructure += "<span></span>";
        modalStructure += "</div>";

        modalStructure += "</div>";

        return modalStructure;
    }


    view_DotOpacityLoader() {

        var modalId = this.modalId;

        var modalStructure = "";

        modalStructure += "<div id='" + modalId + "' class='hhh_modal_loding' >";

        modalStructure += "<div class='dot-opacity-loader'>";
        modalStructure += "<span></span>";
        modalStructure += "<span></span>";
        modalStructure += "<span></span>";
        modalStructure += "</div>";

        modalStructure += "</div>";

        return modalStructure;
    }

    view_BarLoader() {

        var modalId = this.modalId;

        var modalStructure = "";

        var customSpanStyle = "style='background-color: #b66dff;margin:2px;height: 48px;width: 8px;'"

        modalStructure += "<div id='" + modalId + "' class='hhh_modal_loding' >";

        modalStructure += "<div class='bar-loader' style='width:100px;height:100px;' >";
        modalStructure += "<span " + customSpanStyle + " ></span>";
        modalStructure += "<span " + customSpanStyle + " ></span>";
        modalStructure += "<span " + customSpanStyle + " ></span>";
        modalStructure += "<span " + customSpanStyle + " ></span>";
        modalStructure += "</div>";


        modalStructure += "</div>";

        return modalStructure;
    }

    view_SquareBoxLoader() {

        var modalId = this.modalId;

        var modalStructure = "";

        modalStructure += "<div id='" + modalId + "' class='hhh_modal_loding' >";

        modalStructure += "<div class='square-box-loader'>";
        modalStructure += "<div class='square-box-loader-container'>";
        modalStructure += "<div class='square-box-loader-corner-top'></div>";
        modalStructure += "<div class='square-box-loader-corner-bottom'></div>";
        modalStructure += "</div>";
        modalStructure += "<div class='square-box-loader-square'></div>";

        modalStructure += "</div>";

        return modalStructure;
    }

    view_FlipSquareLoader() {

        var modalId = this.modalId;

        var modalStructure = "";

        modalStructure += "<div id='" + modalId + "' class='hhh_modal_loding' >";

        modalStructure += "<div class='flip-square-loader mx-auto text-secondary'></div>";

        modalStructure += "</div>";

        return modalStructure;
    }



    /********************* View Structures END **********************/
}
