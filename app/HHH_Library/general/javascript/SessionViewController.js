class SessionViewController {

    constructor() {

        /*************** Define General Variables *****************/
        this.key_FullScreen = "page_FullScreen";
        this.btnId_FullScreen = "fullscreen-button";

        this.key_MinimizeSidebar = "page_MinimizeSidebar";
        this.btnId_minimizeSidebar = "minimize_sidebar-button";
        /*************** Define General Variables END *****************/

        this.defineMinimizeSidebarVaribles();
        this.defineFullScreenVaribles();
    }

    /********************* Minimize Sidebar **********************/

    /**
    * define Minimize Sidebar Varibles
    */
    defineMinimizeSidebarVaribles() {
        /**
         * Data source comes from:
         *  "public\back_office\assets\js\mix.js"
         *  Search : $('[data-toggle="minimize"]')
         */
        /*************** Define Variables *****************/
        this.btnMinimizeSidebar = document.getElementById(this.btnId_minimizeSidebar);


        this.btnMinimizeSidebar.onclick = function () {
            // save status
            sessionStorage.setItem(this.key_MinimizeSidebar, this.isPageSidebarMinimized());
        }.bind(this);
        /*************** Define Variables END *****************/

        /*************** Keep Last State  *****************/
        if (this.isPageSidebarMinimizedInSession() != this.isPageSidebarMinimized())
            this.btnMinimizeSidebar.click();
        /*************** Keep Last State END  *****************/
    }

    /**
     * This function checks the value stored in the session
     * to see if the user has minimized the page's sidbar.
     *
     * @returns isPageSidebarMinimizedInSession ? true : false;
     */
    isPageSidebarMinimizedInSession() {

        if (sessionStorage.getItem(this.key_MinimizeSidebar) === "true") {
            return true;
        }
        return false;
    }

    /**
     * This function detects whether the page sidebar
     * is currently minimized.
     *
     * @returns isPageSidebarMinimized ? true : false ;
     */
    isPageSidebarMinimized() {

        var bodyElement = document.getElementsByTagName("BODY")[0];
        return func_hasClass(bodyElement, 'sidebar-icon-only');
    }

    /********************* Minimize Sidebar END **********************/

    /********************* Full Screen **********************/
    /**
    * define FullScreen Varibles
    */
    defineFullScreenVaribles() {

        try {

            /**
            * Data source comes from:
            *  "public\back_office\assets\js\mix.js"
            *  Search : $("#fullscreen-button")
            */

            /*************** Define Variables *****************/
            this.btnFullScreen = document.getElementById(this.btnId_FullScreen);

            if (this.btnFullScreen != null) {

                this.btnFullScreen.onclick = function () {
                    /**
                     * Used this "!this.isPageFullScreen()"
                     * Because this value is before change state.
                     */
                    sessionStorage.setItem(this.key_FullScreen, !this.isPageFullScreen());
                    this.setPageFullScreenBtnIcon();
                }.bind(this);
                /*************** Define Variables END *****************/

                /*************** Keep Last State  *****************/
                this.setPageFullScreenBtnIcon();
                document.documentElement.onclick = function () {

                    if (this.isPageFullScreenInSession() != this.isPageFullScreen()) {
                        this.btnFullScreen.click();
                    }
                }.bind(this);
                /*************** Keep Last State END  *****************/
            }


        } catch (error) {
            /* console.log(error); */
        }

    }

    /**
     *  Set page FullScreen icon based on session data.
     */
    setPageFullScreenBtnIcon() {

        var icon = document.getElementById('fullscreen-button');
        icon.className = this.isPageFullScreenInSession() ? "mdi mdi-fullscreen-exit" : "mdi mdi-fullscreen";
    }

    /**
     * This function checks the value stored in the session
     * to see if the user has fully screened the page.
     *
     * @returns isPageFullScreenInSession ? true : false;
     */
    isPageFullScreenInSession() {

        if (sessionStorage.getItem(this.key_FullScreen) == "true") {
            return true;
        }
        return false;
    }
    /**
     * This function detects whether the screen
     * is currently in full screen mode.
     *
     * @returns isPageFullScreen ? true : false ;
     */
    isPageFullScreen() {

        if (
            (document.fullScreenElement !== undefined && document.fullScreenElement === null)
            || (document.msFullscreenElement !== undefined && document.msFullscreenElement === null)
            || (document.mozFullScreen !== undefined && !document.mozFullScreen)
            || (document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen)
        )
            return false;
        else
            return true;
    }
    /********************* Full Screen END **********************/
}
