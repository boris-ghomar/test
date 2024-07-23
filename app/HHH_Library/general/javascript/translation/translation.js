class Translation{

    constructor(locale){
        this.locale = locale;
    }


    translate(key){

        var res = "";
        var locale = 'trans_' + this.locale;

        try {
            res = eval(locale + '.' + key);
        } catch (error) {
            // alert(error.message);
            res = null;
        }

        if( res == null || res == "" || res == undefined){
            var locale_fallback = 'trans_en';
            try {
                res = eval(locale_fallback + '.' + key);
            } catch (error) {
                res = error.message;
            }
        }

        return res;
    }
}

/**
 * Global Section:
 *
 * The "trans" is an alias for translate easier & faster in global
 *
 * You can override the class definition line on your page.
 * Exmpale:
 * {{-- translation:js --}}
 *  @if ( !App::isLocale('en') )
 *  <script src="{{ url(sprintf('js/translation/trans-%s.js',App::getlocale())) }}"></script>
 *  <script>var translation = new Translation('{{ App::getlocale() }}');</script>
 *  @endif
 *  {{-- END translation:js --}}
 */
var translation = new Translation("en");
var trans = function(key){
    return translation.translate(key);
};
