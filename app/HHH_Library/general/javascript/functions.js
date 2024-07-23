/**
* Check if value is empty
*
* @param {string|int} value
* @returns boolean
*/
function func_isEmpty(value) {

    if (value === undefined) return true;
    if (value === null) return true;
    if (value === "") return true;

    return false;
}

/**
* Replace Null with empty text
*
* @param {string} text
* @returns string
*/
function func_replaceNull(text) {

    return text == null ? "" : text;
}

/**
* Convert value to boolean cast
*
* @param {string|boolean} value
* @returns boolean
*/
function func_booleanValue(value) {

    value = String(value).toLowerCase();

    if (value == "true") return true;
    if (value == "1") return true;

    return false;
}

/**
 * Check if element exists
 *
 * @param {viewObject} elementObj
 * @returns boolean
 */
function is_ElementExist(elementObj) {

    if (typeof (elementObj) != 'undefined' && elementObj != null)
        return true;
    else
        return false;
}

/**
 * Check if element exists by element ID
 *
 * @param {string} elementId
 * @returns boolean
 */
function is_ElementExistById(elementId) {
    try {
        return is_ElementExist(document.getElementById(elementId));
    } catch (error) {
        return false;
    }
}

/**
 * Check if view has class
 *
 * @param {viewObject} element
 * @param {string} ReqClassName
 * @returns boolean
 */
function func_hasClass(element, ReqClassName) {

    var hasClass = element.classList.contains(ReqClassName);

    return hasClass === true ? true : false;
}


/**
 * Setup string format
 *
 * example:
 * this.ContainerId = "jsGrid";
 * this.locale = "fa";
 * return "{0}.locale('{1}')".format(this.ContainerId, this.locale);
 *
 * answer: jsGrid.locale('fa')
 */
function setupStringFormat() {

    // First, checks if it isn't implemented yet.
    if (!String.prototype.format) {
        String.prototype.format = function () {
            var args = arguments;
            return this.replace(/{(\d+)}/g, function (match, number) {
                return typeof args[number] != 'undefined'
                    ? args[number]
                    : match
                    ;
            });
        };
    }

}

/**
 * Toggle password visibility
 *
 * @param {string} inputId
 * @param {string} passwordShowId
 * @param {string} passwordHiddenId
 */
function togglePasswordVisibility(inputId, passwordShowId, passwordHiddenId) {

    if (is_ElementExistById(inputId)) {

        var passwordInput = document.getElementById(inputId);

        if (passwordInput.type === "password") {

            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }

        toggleElementClass(passwordShowId, 'd-none');
        toggleElementClass(passwordHiddenId, 'd-none');
    }

}

/**
 * Toggle element class
 *
 * @param {string} elementId
 * @param {string} className
 */
function toggleElementClass(elementId, className) {

    if (is_ElementExistById(elementId)) {

        var element = document.getElementById(elementId);
        element.classList.toggle(className);
    }
}

/**
* Get view template HTML
*
* @param {string} viewId
* @param {boolean} showError
* @returns viewObj|null
*/
function func_getView(viewId, showError = false) {

    let errorMsg = "No view found with this ID:" + viewId;

    try {
        if (is_ElementExistById(viewId))
            return document.getElementById(viewId);

    } catch (error) {
        errorMsg += "\n" + "Error:\n" + error;
    }

    if (showError)
        console.error(errorMsg);

    return null;
}

/**
* Display view
*
* @param {string} viewId
* @param {boolean} show
*/
function func_displayView(viewId, show = true) {

    var view = func_getView(viewId);

    if (view !== null) {

        var hideClass = 'd-none';
        var isHidden = func_hasClass(view, hideClass);

        if (show && isHidden)
            toggleElementClass(viewId, hideClass);
        else if (!show && !isHidden)
            toggleElementClass(viewId, hideClass);
    }
}

/**
* Remove view by id
*
* @param {string} viewId
*/
function func_removeView(viewId) {

    var view = func_getView(viewId);

    if (view != null) {
        view.parentNode.removeChild(view);
        func_removeView(viewId);
    }
}

/**
* Get a temporary ID
*
* @param {string} prefix
* @returns string
*/
function func_getTemporaryID(prefix = null) {

    var rnd = Math.floor(Math.random() * 100) + 1;
    var id = Date.now() + "" + rnd;

    if (prefix != null) {

        prefix = prefix.trim();
        id = prefix + id;
    }

    return is_ElementExistById(id) ? func_getTemporaryID(prefix) : id;
}

/**
 * Copy text to clipboard
 *
 * @param {string} text
 */
function func_copyToClipboard(text) {

    navigator.clipboard.writeText(text);
}

/**
 * This function created to use
 * for key => value arrays
 *
 * Example:
 * func_forEach(cities, (key, value) => {
 *             console.log(key);
 *             console.log(value);
 *         });
 *
 * @param {array} arrayList key => value
 * @param {function} actionFunc action function for each item
 */
function func_forEach(array, actionFunc) {

    for (const [key, value] of Object.entries(array)) {

        actionFunc(key, value);
    }
}
