/**
 * JSONViewer - by Hossein Nateghian 2022 (c) .
 */
class JsonViewerController {

    /**
     *
     * @param {*} jsonTextareaId textarea obj ID that contains json string (in HTML)
     * @param {*} jsonViewerId  viewer element id (in HTML)
     * @param {*} jsonViewType  view type
     */
    constructor(jsonTextareaId, jsonViewerId, jsonViewType) {

        /* const */
        this.jsonViewType_expand = 0;
        this.jsonViewType_collapse = 1;
        this.jsonViewType_JsutLevel1 = 2;

        this.jsonTextareaId = jsonTextareaId;
        this.jsonViewerId = jsonViewerId;
        this.jsonViewType = jsonViewType;

    }

    createView() {

        var jsonObj = {};
        var jsonViewer = new JSONViewer();

        document.getElementById(this.jsonViewerId).appendChild(jsonViewer.getContainer());

        var jsonContainerObj = document.getElementById(this.jsonTextareaId);

        // jsonContainer value to JSON object
        var setJSON = function () {
            try {
                var value = jsonContainerObj.value;
                jsonObj = JSON.parse(value);
            }
            catch (err) {
                alert(err);
            }
        };

        // load default value
        setJSON();

        switch (this.jsonViewType) {

            default:
            case this.jsonViewType_expand:
                setJSON();
                jsonViewer.showJSON(jsonObj);
                break;
            case this.jsonViewType_collapse:
                jsonViewer.showJSON(jsonObj, null, 1);
                break;
            case this.jsonViewType_JsutLevel1:
                jsonViewer.showJSON(jsonObj, 1);
                break;
        }

    }

}
