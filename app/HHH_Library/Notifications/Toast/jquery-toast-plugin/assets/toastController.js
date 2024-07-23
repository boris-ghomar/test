/*
based on:
https://github.com/kamranahmedse/jquery-toast-plugin

online test configs:
https://kamranahmed.info/toast

downloaded demo:
file:///G:/Programing/Web/FrameWork/Widget/Notifications/jquery-toast-plugin/demos/index.html

---------------------------------------------------------------------------------------------------------

position property can be used to specify the position. There are following predefined positions which you can use:

    bottom-left value to show the toast at bottom left position
    bottom-right value to show the toast at bottom right position
    bottom-center value to..
    top-right value to..
    top-left value to..
    top-center value to..
    mid-center value to show the toast at middel center position
    { top: '-', bottom: '-', left: '-', right: '-' } javascript object with positioning properties as you set in CSS

    example:
        <button type="button" class="btn btn-gradient-success btn-fw"
        onclick="showSuccessToast('test',{ top: 100, bottom: '-', left: 200, right: '-' } )">Success</button>
*/


(function ($) {

    var defultShowToastTime = 5000; //ms : 5 seconds

    showSuccessToast = function (message, position) {
        'use strict';
        resetToastPosition();
        $.toast({
            heading: trans('result.success'),
            text: String(message),
            showHideTransition: 'slide',
            icon: 'success',
            loaderBg: '#f96868',
            hideAfter: defultShowToastTime,
            position: getPosition(position)
        })
    };

    showInfoToast = function (message, position) {
        'use strict';
        resetToastPosition();
        $.toast({
            heading: trans('general.info'),
            text: String(message),
            showHideTransition: 'slide',
            icon: 'info',
            loaderBg: '#46c35f',
            hideAfter: defultShowToastTime,
            position: getPosition(position)
        })
    };
    showWarningToast = function (message, position) {
        'use strict';
        resetToastPosition();
        $.toast({
            heading: trans('general.warning'),
            text: String(message),
            showHideTransition: 'slide',
            icon: 'warning',
            loaderBg: '#57c7d4',
            hideAfter: defultShowToastTime,
            position: getPosition(position)
        })
    };
    showDangerToast = function (message, position) {
        'use strict';
        resetToastPosition();
        $.toast({
            heading: trans('general.danger'),
            text: String(message),
            showHideTransition: 'slide',
            icon: 'error',
            loaderBg: '#f2a654',
            hideAfter: defultShowToastTime,
            position: getPosition(position)
        })
    };


    resetToastPosition = function () {
        $('.jq-toast-wrap').removeClass('bottom-left bottom-right top-left top-right mid-center'); // to remove previous position class
        $(".jq-toast-wrap").css({
            "top": "",
            "left": "",
            "bottom": "",
            "right": ""
        }); //to remove previous position style
    }

    getPosition = function (position) {

        if (position !== undefined && position !== null && position !== "") {
            return position;
        }
        ///////////////////////////////////////
        //defaults
        if (direction == "rtl")
            return 'top-right';

        return 'top-left';
    }

})(jQuery);

