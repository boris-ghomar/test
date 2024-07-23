/* WidgetInputGroup [START] */
class WidgetInputGroup {

    /**
     * constructor
     *
     * @param {string} attributeName
     * @param {int} maxCount
     */
    constructor(attributeName, maxCount = 5) {

        this.attributeName = attributeName;
        this.maxCount = maxCount;

        this.addExistingFields();
    }

    /**
     * Add existing fields
     */
    addExistingFields() {
        let existingValues = JSON.parse(func_getView('Existing_' + this.attributeName).value);

        if (!func_isEmpty(existingValues)) {
            existingValues.forEach(filedValue => {
                this.addNewField(filedValue);
            });
        }
    }

    /**
     * Add new field to view
     */
    addNewField(filedValue = null) {

        let templateId = func_getTemporaryID();

        let fieldsDisplaySection = func_getView(this.attributeName + '_DisplaySection');
        let newFiledTemplate = func_getView(this.attributeName + '_NewFiledTemplate').innerHTML;

        newFiledTemplate = newFiledTemplate.replaceAll('TemplateID_' + this.attributeName, templateId);

        let inputFieldsIndex = document.getElementsByName(this.attributeName + '[]').length;

        fieldsDisplaySection.innerHTML += newFiledTemplate;

        let filedInputView = func_getView('filedInput_' + templateId);
        filedInputView.setAttribute('name', this.attributeName + '[]');

        if (!func_isEmpty(filedValue)) {
            filedInputView.setAttribute('value', filedValue);
        }

        let fieldErrorViewId = this.attributeName + '.' + inputFieldsIndex;
        if (is_ElementExistById(fieldErrorViewId)) {

            let filedInputSectionView = func_getView('filedInputSection_' + templateId);
            filedInputSectionView.innerHTML += func_getView(fieldErrorViewId).innerHTML;

            /*
            * Remove the hidden error view after use so that it is not displayed
            * for a new field when the user adds a new field.
            */
            func_removeView(fieldErrorViewId);

            let invalidCssClass = "is-invalid";
            if (!func_hasClass(filedInputView, invalidCssClass))
                toggleElementClass(filedInputView.id, invalidCssClass);
        }

        this.limitFiledsCount();
    }

    /**
     * Delete field
     */
    deleteField(filedId) {

        func_removeView(filedId);
        this.limitFiledsCount();
    }

    /**
     * Check limit of fileds count
     */
    limitFiledsCount() {
        let addNewFieldBtnId = this.attributeName + '_addNewFieldBtn';

        let fileds = document.getElementsByName(this.attributeName + '_Filed');

        /* +1 added for template name */
        func_displayView(addNewFieldBtnId, fileds.length < (this.maxCount + 1));
    }
}
/* WidgetInputGroup [END] */
