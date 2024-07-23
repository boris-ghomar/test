const mix = require('laravel-mix');

const tailwindcss = require('tailwindcss');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */


// HHH Disabled



//HHH

/**
 * npm run dev
 * npm run production
 * npm run watch
 * npm cache clean -f
 */


/*********************** copying ****************************/
mix.copyDirectory('app/HHH_Library/general/CSS', 'public/assets/general/css');

mix.copyDirectory('resources/hhh_assets', 'public/assets');

mix.copyDirectory('app/HHH_Library/jsGrid/assets/jsgrid', 'public/assets/general/widgets/jsgrid');

mix.copyDirectory('app/HHH_Library/Notifications/Toast/jquery-toast-plugin/assets/public', 'public/assets/general/widgets/jquery-toast-plugin');

mix.copyDirectory('app/HHH_Library/bootstrap_clockpicker/public', 'public/assets/general/widgets/bootstrap_clockpicker');

mix.copyDirectory('app/HHH_Library/Charts/ChartJs/public', 'public/assets/general/widgets/charts/chart_js');

// mix.copyDirectory('app/HHH_Library/SweetAlert/assets/public','public/widgets/sweetalert');
/*********************** copying END ****************************/

mix.styles([

    'resources/css/hhh/general/bootstrap.css',
    'resources/css/hhh/site/app.css',
    'resources/css/hhh/site/app_mobile.css',
    'resources/css/hhh/site/template.css',
    'resources/css/hhh/site/template_mobile.css',
    'resources/css/hhh/site/quill_editor.css',
    'app/HHH_Library/Notifications/Toast/jquery-toast-plugin/assets/reformation/reform.css',
    'app/HHH_Library/Notifications/Toast/jquery-toast-plugin/assets/reformation/dark_theme.css',
    'app/HHH_Library/modalBox/css/modal_box.css',
    'app/HHH_Library/modalBox/css/dark_theme.css',
    'app/HHH_Library/modalBox_loading/css/modal_box_loading.css',
    'app/HHH_Library/modalBox_loading/css/dark_theme.css',
    'app/HHH_Library/jsonViewerMaster/src/json-viewer.css',

], 'public/assets/site/resources/css/app.css');


mix.styles([
    'resources/css/hhh/site/cf_style.css', // Small style for cloudflare page

], 'public/assets/site/resources/css/cf_style.css');

mix.styles([

    'public/vendor/log-viewer/app.css', // origin css
    'resources/css/hhh/site/log-viewer.css', // modify css


], 'public/vendor/log-viewer/app.css');

mix.styles([
    /**
     * These files are added to the project when needed
     *  and should not be in the code at other times,
     *  so avoid merging this file with other files.
     */
    'app/HHH_Library/general/CSS/fonts/farsi_fonts.css',

], 'public/assets/general/css/fonts/farsi_fonts.css');

mix.styles([
    /**
     * These files are added to the project when needed
     *  and should not be in the code at other times,
     *  so avoid merging this file with other files.
     */
    'resources/css/hhh/general/rtl_override.css',

], 'public/assets/general/css/rtl_override.css');

/**
* These files are added to the project when needed.
*
*/
/********* dashboard *********/
mix.styles([

    'resources/css/hhh/site/dashboard.css',

], 'public/assets/general/css/site_dashboard.min.css');

/********* chatbot *********/
mix.styles([

    'resources/css/hhh/site/chatbot.css',

], 'public/assets/general/css/chatbot.min.css');

/********* messenger *********/
mix.styles([

    'resources/css/hhh/site/messenger.css',

], 'public/assets/general/css/messenger.min.css');

/********* dashboard *********/
mix.styles([
    'resources/css/hhh/site/referral_panel.css',

], 'public/assets/general/css/referral_panel.min.css');

/********* chartJs *********/
mix.styles([
    'app/HHH_Library/Charts/ChartJs/ChartJs.css',

], 'public/assets/general/widgets/charts/chart_js/css/chart_js.min.css');

/*********************************** BackOffice ***********************************/

/********* My Files *********/


mix.scripts([

    'app/HHH_Library/general/javascript/functions.js',
    'app/HHH_Library/general/javascript/ServerConnection.js',
    'app/HHH_Library/general/javascript/session.js',
    'app/HHH_Library/general/javascript/WidgetInputGroup.js',
    'app/HHH_Library/general/javascript/SessionViewController.js',
    'app/HHH_Library/general/javascript/translation/translation.js',
    'app/HHH_Library/modalBox/js/modal_box.js',
    'app/HHH_Library/modalBox/js/modal_realize.js',
    'app/HHH_Library/modalBox/js/modal_confirm.js',
    'app/HHH_Library/modalBox/js/modal_custom.js',
    'app/HHH_Library/modalBox/js/modal_image_zoom.js',
    'app/HHH_Library/modalBox_loading/js/modal_box_loading.js',
    'app/HHH_Library/jsonViewerMaster/src/json-viewer.js',
    'app/HHH_Library/jsonViewerMaster/src/json-viewer-controller.js',

], 'public/assets/general/js/app.js');


/********* toast *********/
mix.scripts([
    'app/HHH_Library/Notifications/Toast/jquery-toast-plugin/assets/toastController.js',

], 'public/assets/general/widgets/jquery-toast-plugin/toast_controller.js');

/********* jsGrid *********/
mix.styles([
    'app/HHH_Library/jsGrid/assets/reformation/reform.css',
    'app/HHH_Library/jsGrid/assets/reformation/DarkTheme.css',

], 'public/assets/general/widgets/jsgrid/jsgrid-modify.min.css');

mix.scripts([
    'app/HHH_Library/jsGrid/jsgrid-ctrl.js',
    'app/HHH_Library/jsGrid/assets/customFields/date.js',
    'app/HHH_Library/jsGrid/assets/customFields/date_range.js',
    'app/HHH_Library/jsGrid/assets/customFields/number_range.js',

], 'public/assets/general/widgets/jsgrid/jsgrid-ctrl.min.js');


/********* translation files *********/
mix.scripts('app/HHH_Library/general/javascript/translation/lang/trans-en.js',
    'public/assets/general/js/translation/trans-en.js');
mix.scripts('app/HHH_Library/general/javascript/translation/lang/trans-fa.js',
    'public/assets/general/js/translation/trans-fa.js');


/********* Betconstruct *********/
mix.styles([
    'resources/js/hhh/ThisApp/Betconstruct/SwarmApi.js',

], 'public/assets/general/js/swarm_api.min.js');

mix.styles([
    'resources/js/hhh/ThisApp/Betconstruct/BcLogin.js',

], 'public/assets/general/js/bc_login.min.js');

/********* Site Actions *********/
mix.styles([
    'resources/js/hhh/ThisApp/PostAction.js',

], 'public/assets/general/js/post_action.min.js');

/********* chatbotCreator files *********/
mix.scripts([
    'resources/js/hhh/ThisApp/Chatbot/chatbotCreator.js',

], 'public/assets/general/js/chatbot_creator.min.js');

/********* chatbotMessenger files *********/
mix.scripts([
    'resources/js/hhh/ThisApp/Chatbot/chatbotMessenger.js',

], 'public/assets/general/js/chatbot_messenger.min.js');

/********* chatbotMessenger files *********/
mix.scripts([
    'resources/js/hhh/ThisApp/Tickets/TicketMessenger.js',

], 'public/assets/general/js/ticket_messenger.min.js');

/********* DomainsImporter files *********/
mix.scripts([
    'resources/js/hhh/ThisApp/Domains/DomainsImporter.js',

], 'public/assets/general/js/domains_importer.min.js');

/********* DomainPreparingReviewer files *********/
mix.scripts([
    'resources/js/hhh/ThisApp/Domains/DomainPreparingReviewer.js',

], 'public/assets/general/js/domain_preparing_reviewer.min.js');

/********* Dashboard files *********/
mix.scripts([
    'resources/js/hhh/ThisApp/Dashboard/SuperDashboard.js',
    'resources/js/hhh/ThisApp/Dashboard/DomainController.js',

], 'public/assets/general/js/site_dashboard.min.js');

/********* Client Profile files *********/
mix.scripts([
    'resources/js/hhh/ThisApp/Profile/ClientProfileController.js',

], 'public/assets/general/js/client_profile_controller.min.js');

/********* Registration files *********/
mix.scripts([
    'resources/js/hhh/ThisApp/Auth/Registration/RegistrationController.js',

], 'public/assets/general/js/registration_controller.min.js');


mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
    ]);


// mix.styles([

//     'resources/views/back_office/assets/vendors/mdi/css/materialdesignicons.min.css',
//     'resources/views/back_office/assets/vendors/flag-icon-css/css/flag-icon.min.css',
//     'resources/views/back_office/assets/vendors/css/vendor.bundle.base.css',
//     'resources/views/back_office/assets/vendors/jquery-bar-rating/css-stars.css',
//     'resources/views/back_office/assets/vendors/font-awesome/css/font-awesome.min.css',
//     'resources/views/back_office/assets/css/demo_1/style.css',

// ], 'public/back_office/assets/css/vertical_template/app.css');

// mix.scripts([
//     'resources/views/back_office/assets/vendors/js/vendor.bundle.base.js',
//     'resources/views/back_office/assets/vendors/jquery-bar-rating/jquery.barrating.min.js',
//     'resources/views/back_office/assets/vendors/chart.js/Chart.min.js',
//     'resources/views/back_office/assets/vendors/flot/jquery.flot.js',
//     'resources/views/back_office/assets/vendors/flot/jquery.flot.resize.js',
//     'resources/views/back_office/assets/vendors/flot/jquery.flot.categories.js',
//     'resources/views/back_office/assets/vendors/flot/jquery.flot.fillbetween.js',
//     'resources/views/back_office/assets/vendors/flot/jquery.flot.stack.js',
//     'resources/views/back_office/assets/js/off-canvas.js',
//     'resources/views/back_office/assets/js/hoverable-collapse.js',
//     'resources/views/back_office/assets/js/misc.js',
//     'resources/views/back_office/assets/js/settings.js',
//     'resources/views/back_office/assets/js/todolist.js',
//     'resources/views/back_office/assets/js/dashboard.js',

// ], 'public/back_office/assets/js/app.js');

/*********************************** BackOffice END ***********************************/

//HHH END
