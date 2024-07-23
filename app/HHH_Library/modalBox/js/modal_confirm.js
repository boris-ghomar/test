class ModalConfirm extends ModalBox {

    constructor(modalId) {
        super(modalId);

        this.showElement(this.btnYes);
        this.showElement(this.btnNo);
    }
}
