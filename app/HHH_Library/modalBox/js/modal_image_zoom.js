class ModalZoomImage {
    /*
     * For zoom image
    */

    constructor() {

        this.modalId = "ModalZoomImage";

        this.modal = this.makeModalWindow();
    }

    /**
     * Set image URL
     *
     * @param {string} imageUrl
     */
    setImage(imageUrl) {
        this.imageUrl = imageUrl;
    }

    /**
     * Make modal window
     *
     * @returns viewObject
     */
    makeModalWindow() {

        var modalView = document.createElement("div");
        modalView.innerHTML = this.getModalStructure();
        document.body.appendChild(modalView);

        this.setupDialogClose();
        return document.getElementById(this.modalId);
    }

    /**
     * Setup dialog close logics an d events
     */
    setupDialogClose() {

        /* When the user clicks on <span> (x), close the modal */
        var btnClose = document.getElementById("close_" + this.modalId);
        btnClose.onclick = function () {
            this.close();
        }.bind(this);

        /* When the user clicks anywhere outside of the modal, close it */
        window.onclick = function (event) {
            if (event.target == this.modal) {
                this.close();
            }
        }.bind(this);
    }

    /********************* Internal methods **********************/

    /**
     * Show modal
     */
    show() {

        var imageId = this.modalId + "_image";
        var imageView = document.getElementById(imageId);

        if (!is_ElementExist(imageView)) {

            imageView = document.createElement("img");
            imageView.id = imageId;
            this.modal.appendChild(imageView);

        }
        imageView.src = this.imageUrl;

        var onloadImage = () => {
            this.fitImageToScreen(imageView)
        };
        imageView.addEventListener('load', onloadImage);


        /* Display */
        this.modal.style.display = "flex";
    }

    /**
     * Close modal
     */
    close() {
        this.modal.style.display = "none";
    }

    /**
     * Fit image to screen based on image oriantiation
     *
     * @param {viewObject} imageView
     */
    fitImageToScreen(imageView) {

        var isScreenVertical = screen.height > screen.width ? true : false;
        this.modal.dataset.vertical = isScreenVertical;
    }
    /********************* Internal methods END **********************/

    /********************* View Structures  **********************/
    /**
     * Get modal base structure
     *
     * @returns viewObject
     */
    getModalStructure() {

        var modalStructure = "";

        modalStructure += "<div id='" + this.modalId + "' class='hhh_modal_zoom' >";
        modalStructure += "<div class='close-hhh_modal' >";
        modalStructure += "<span id='close_" + this.modalId + "'>&times;</span>";
        modalStructure += "</div>";
        modalStructure += "</div>";

        return modalStructure;

    }

    /********************* View Structures END **********************/


}
