<!-- <div class="brand-logo mb-5">
    <img class="brand-logo-errorpage" src="{{ App\Enums\Settings\AppSettingsEnum::CommunityBigLogo->getImageUrl() }}">
</div>

<div class="row mt-5 @lang('general.locale.direction')" style='font-family:"Manrope-regular"'>
    <div class="col-12 text-center mt-xl-2 w-100">
        <div class="display-4 mb-0 font-weight-medium">@lang('thisApp.CustomPages.IpRestriction.GetSiteURLDescr')</div>

        <br />
        <a type="button" class="btn btn-primary btn-icon-text font-weight-bold mt-3"
            href="{{ SitePublicRoutesEnum::IpRestrictionRedirect->route() }}">
            {{-- <i class="mdi mdi-logout btn-icon-prepend"></i> --}}
            @lang('thisApp.CustomPages.IpRestriction.GetSiteURL')
        </a>
    </div>
</div> -->


  <style>

    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@100;400&display=swap');


@media (max-width: 320px) {

    .country-table img {
        width: 15px !important;
        height: 9px !important;
    }

    .country-table {
        margin-top: 14px !important;
        text-align: center !important;
        font-size: 9px !important;
    }
}

@media (max-width: 360px) {

    .bg-image img {
        margin-top: 150px !important;
        margin-bottom: 0px !important;
        width: 95px !important;
    }

    .btn {
        padding: 2px 3px 2px 3px !important;
        margin-top: 10px !important;
        width: 65px !important;
        font-size: 0.6rem !important;
    }

    .country-table {
        margin-top: 10px !important;
        font-size: 7px !important;
    }

    .country-table img {
        width: 10px !important;
        height: 5px !important;
    }

    .persian-content h1 {
        font-size: 8px !important;
    }

    .english-content h2 {
        font-size: 8px !important;
        margin-bottom: 0px !important;
    }

    .customer-support h3 {
        font-size: 6px !important;
        margin-top: 10px !important;
    }
}

@media (max-width: 375px) {

    .bg-image img {
        margin-top: 150px !important;
        margin-bottom: 0px !important;
        width: 95px !important;
    }

    .btn {
        padding: 2px 3px 2px 3px !important;
        margin-top: 10px !important;
        width: 65px !important;
        font-size: 0.6rem !important;
    }

    .country-table {
        margin-top: 10px !important;
        font-size: 7px !important;
    }

    .country-table img {
        width: 14px !important;
        height: 9px !important;
    }

    .persian-content h1 {
        font-size: 8px !important;
    }

    .english-content h2 {
        font-size: 8px !important;
        margin-bottom: 0px !important;
    }

    .customer-support h3 {
        font-size: 6px !important;
        margin-top: 10px !important;
    }
}

@media (max-width: 540px) {

    .bg-image img {
        margin-top: 100px !important;
        margin-bottom: 0px !important;
        width: 85px !important;
    }

    .btn {
        padding: 9px 7px 9px 5px !important;
        margin-top: 20px !important;
        width: 105px !important;
        font-size: 0.8rem !important;
    }

    .country-table {
        margin-top: 10px !important;
        font-size: 9px !important;
    }

    .persian-content h1 {
        font-size: 11px !important;
        margin-bottom: 0px;
    }

    .english-content h2 {
        font-size: 11px !important;
        margin-bottom: 20px !important;
    }

    .customer-support h3 {
        font-size: 13px !important;
    }
}

@media (max-width: 576px) {

    .persian-content h1 {
        font-size: 11px !important;
    }

    .english-content h2 {
        font-size: 11px !important;
    }

    .bg-image img {
        margin-bottom: 10px !important;
        width: 145px !important;
    }

    .customer-support h3 {
        font-size: 11px !important;
    }

    .btn {
        padding: 5px 0px 5px 0px !important;
        margin-top: 5px !important;
        width: 95px !important;
    }
}

@media (max-width: 768px) {

    .flag {
        width: 15px !important;
        height: 10px !important;
    }

    .country-fa {
        font-size: 10px !important;
    }

    .country-en {
        font-size: 9px !important;
    }

    .persian-content h1 {
        font-size: 9px !important;
    }

    .english-content h2 {
        font-size: 9px !important;
    }

    .bg-image img {
        margin: 200px 0px 35px auto !important;
    }

    .customer-support h3 {
        font-size: 9px !important;
    }

    .btn {
        padding: 5px 0px 5px 0px !important;
        margin-top: 5px !important;
        width: 100px !important;
    }
}

@media (max-width: 922px) {

    .bg-image img {
        margin-bottom: 10px !important;
        width: 145px !important;
        margin-top: 100px !important;
    }
}

@media (max-width: 1024px) {

    .bg-image img {
        margin-top: 150px !important;
        margin-bottom: 0px !important;
        width: 145px !important;
    }

    .btn {
        padding: 9px 7px 9px 5px !important;
        margin-top: 25px !important;
        width: 145px !important;
        font-size: 0.8rem !important;
        margin-top: 10px !important;
    }

    .country-table {
        font-size: 14px !important;
    }

    .flag {
        width: 22px !important;
        height: 13px !important;
    }

    .customer-support h3 {
        font-size: 10px !important;
        margin-top: 20px !important;
    }
}


/* ----------Global CSS ----------*/

* {
    box-sizing: border-box;
    padding: 0;
    margin: 0;
}

.row:after {
    content: "";
    clear: both;
    display: block;
}

button:focus,
button:active button:hover .btn:active {
    outline: 0px !important;
    box-shadow: none !important;
}

body {
    padding: 0px 25px 0px 25px;
    background-color: rgb(36, 36, 36);
    font-family: 'Noto Sans Arabic', 'Montserrat', sans-serif;
    color: #cecdcd;
    direction: rtl;
    offset: "0";
}

img {
    border-radius: 0.5px;
}

.bg-image img {
    margin-bottom: 10px;
    width: 145px;
    margin-top: 200px;
}

body:not(.btn) a:any-link {
    text-decoration: underline;
    color: rgb(172, 171, 171);
    font-weight: bold;
}

.btn {
    text-decoration: none !important;
    color: #f0f0f0 !important;
    font-weight: lighter !important;
    word-spacing: 1px;
    padding: 9px 7px 9px 5px;
    margin-top: 20px;
    background-color: #37944a;
    width: 150px;
    font-size: 0.8rem;
}

.persian-content h1 {
    font-size: 15px;
    direction: rtl;
    line-break: var(2px);
}

.english-content h2 {
    margin-top: 17px;
    font-size: 15px;
    direction: ltr;
}

.icons {
    margin-top: 0px;
    width: 35px;
}

.flag {
    width: 22px;
    height: 15px;
}

th {
    text-align: center;
    width: auto;
}

td {
    text-align: center;
    height: auto;
    vertical-align: center;
}

.country-table {
    margin-top: 17px;
    text-align: center;
    font-size: 12px;
}

.customer-support h3 {
    color: grey;
    font-size: 12px;
    margin-top: 20px;
}

.Social-Media {
    display: none;
}
  </style> 


  <Section class="vpn">

    <Section title="Logo">
      <dev class="site-header container-fluid text-center">
        <div class="row justify-content-center align-items-center text-center">
          <div class="col-auto">
            <div class="bg-image">
              <img src="https://bclanding.xyz/wp-content/uploads/2021/07/betcart_logo-200x55.png" alt="لوگوی بتکارت"
                title="لوگوی بتکارت">
            </div>
          </div>
        </div>
      </dev>
    </Section>

    <Section class="persian-content" title="Persian Content">
      <div class="container-fluid text-lighter">
        <div class="row justify-content-center align-items-center text-center">
          <div class="col-auto persian-content">
            <h1>
              ورود به سایت بتکارت از کشورهای زیر ممکن نمی باشد.
              <br><br>
              در صورتی که از فیلترشکن استفاده می‌کنید، لطفاً کشور را در فیلترشکن تغییر دهید و سپس صفحه را رفرش نمایید.
            </h1>
          </div>
        </div>
      </div>
      </div>
    </Section>

    <Section class="country-table" title="Country Flag">
      <div class="container-fluid text-center">
        <div class="row justify-content-center align-items-justify">
          <div class="col-auto">
            <div>
              <table class="center">
                
                <tr>
                  <th><img
                      src="data:image/png;base64,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"
                      alt="USA | آمریکا" title="USA flag | پرچم امریکا " class="flag"></th>
                  <th><img
                      src="data:image/png;base64,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"
                      alt="United Kingdom | انگلستان" title="UK flag | پرچم انگلیس، " class="flag"></th>
                  <th><img
                      src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAhCAYAAACfiCi5AAAAAXNSR0IArs4c6QAAAERlWElmTU0AKgAAAAgAAYdpAAQAAAABAAAAGgAAAAAAA6ABAAMAAAABAAEAAKACAAQAAAABAAAAMKADAAQAAAABAAAAIQAAAACGt9I+AAADYklEQVRYCe2YzWscZRzHP8/MZGZn35LNZtOY6DZNIU3UQGIUYoyHUEHsRTyKoCcv4tFjPXnx5sn/QEFEQZGCKEil1lhxG202VWqiDTZptmm6m+xmd3Z2XnxWz4XZ3UxCYB8YnuGZ+T3zfXm+zwMjls6ce48T2nwhftc83794QvE3YX+pnGDw/0HvEjhuB7sOdB3oUAFtcLHa4RTHV27d1ZPCvZLwjw9CZ18ufBu/2VGIG55KtQ7lYzRRa0eD+3sK19fg2nqM4m4VXbF5ZgJeeV5B6UiS1tG0TODyis6HH1msF2BmEjY3XOLJCJd/qXJpCd59Q3DmEdE6kjYrWtbret7hVD3JS+P9iCKowsBwNM71R+VScvhxxeVe8ehiFZjAp5d6ePZNgW44TC9Y/LxTY0sun2bbcWy26xWyGZ/3P9Y4/7ZFsdympC2WBSawvKMwlDX4+guB9lWS1yMJXh4Y4q2LH5AdXcDWp8iODDAzFUWPalx4p8qf/4TvROAMvLgwxak7Z0kOfoa/rfL3uIPdEFRza6gFheknZnlqokAj/SpzE58TEdsMD4SfhWAOeOBdKzFZXiXl9VK2DApWP27Bx+xxGck+yuLsPDe/M0j/luNJGerFuQRV32xxQbT+eiAHfClk79M6u5UIMaOHhNWg+JdNbfAFtnMrWEaZgxslXnvOZ6U0gzb2DUUvxV5NkInWWkfVQkUgB4QksL/UIJKyUBNxHMfjtFondneVO38sYxZXyZSvoo7oDEZv4e36aIonL5+aPOjCbIEcaAJwpmNs7mcxK3l6IyoZX8M0t2jU6xiGTjStIofxTz/Oei7PmFrClGE2U2HCh8AEbLGFUy7huGU8DJJRlVRsgMdqFfRRm+GYidAPqGx9j61a3N60uTDnSvThBjkwgdzGGA/2FYZ7llmcT7BfPsu9zDh5fYN57YC+kTUK9+P8kE+TkLMOaTdI94YLvultYALD4grpKMgMY/QdcH72Nsu3pNr9Hk4c+jIqhQcFJuMyA3LXiujhg28SEJVPUoFOG1eCal66pNzsVRn/vapPbs1lekyVu5M84GQGmuO283/fvA+z7f4U/VVcHR0PRCBMIG3PLUT3v1Db4h1WYcir9LBgPnyeLoGHa3M0T068AxpCVI5Gq8P/iuL7tX8BlLcYQDnL64AAAAAASUVORK5CYII="
                      alt="Spain | اسپانیا" title="ُSpain flag | پرچم اسپانیا " class="flag"> </th>
                  <th><img
                      src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWCAYAAAA8AXHiAAAAAXNSR0IArs4c6QAAAsZJREFUeF7t3bENAjEQRUG2DxIyKqE5eqHC44xEDX7RjQv4kp8m3/ncn+vmKbC5wIC1uai5fwGwQEgKgJVkNQoWA0kBsJKsRsFiICkAVpLVKFgMJAXASrIaBYuBpABYSVajYDGQFAAryWoULAaSAmAlWY2CxUBSAKwkq1GwGEgKgJVkNQoWA0kBsJKsRsFiICkAVpLVKFgMJAXASrIaBYuBpABYSVajYDGQFAAryWoULAaSAmAlWY2CxUBSAKwkq1GwGEgKgJVkNQoWA0kBsJKsRsFiICkAVpLVKFgMJAXASrIaBYuBpABYSVajYDGQFAAryWoULAaSAmAlWY2CxUBSAKwkq9E5j8OFVQ62F5i1FljbsxoEi4GkAFhJVqNgMZAUACvJahQsBpICYCVZjYLFQFIArCSrUbAYSAqAlWQ1ChYDSQGwkqxGwWIgKQBWktUoWAwkBcBKshoFi4GkAFhJVqNgMZAUACvJahQsBpICYCVZjYLFQFIArCSrUbAYSAqAlWQ1ChYDSQGwkqxGwWIgKQBWktUoWAwkBcBKshoFi4GkAFhJVqNgMZAUACvJahQsBpICYCVZjYLFQFIArCSrUbAYSAqAlWQ1ChYDSQGwkqxGwWIgKQBWktUoWAwkBeZ7nu4VJmmvPTqP1xusaxtIfg9WktUoWAwkBcBKshoFi4GkAFhJVqNgMZAUACvJahQsBpICYCVZjYLFQFIArCSrUbAYSAqAlWQ1ChYDSQGwkqxGwWIgKQBWktUoWAwkBcBKshoFi4GkAFhJVqNgMZAUACvJahQsBpICYCVZjYLFQFIArCSrUbAYSAqAlWQ1ChYDSQGwkqxGwWIgKQBWktUoWAwkBcBKshoFi4GkAFhJVqNgMZAUACvJahQsBpICYCVZjYLFQFIArCSrUbAYSAqAlWQ1ChYDSQGwkqxGwWIgKQBWktXoD+MCiffqlZ7bAAAAAElFTkSuQmCC"
                      alt="Netherlands | هلند" title="ُNetherlands flag | پرچم هلند " class="flag"></th>
                  <th><img
                      src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWCAYAAAA8AXHiAAAAAXNSR0IArs4c6QAAA1VJREFUeF7t18ERgkAUBcEPaXg3AQMkTBLxKnqQIBpqiGC3euvVsMxz+84Nvs++zbou177Jccz78br2Hc7TLz0syLGHBWGcR2mxLJMWS/JosSSN/1laLMukxZI8WixJo8XyNGZaLEmlxZI0WixPo8WyTFosy6O/Qs+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy6TFsjxqLM+jxpJMWixJo8byNGosy+RGi/UDZqA8z1bCEZ4AAAAASUVORK5CYII="
                      alt="France | فرانسه" title="ُFrance flag | پرچم فرانسه " class="flag"></th>
                  <th><img
                      src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWCAYAAAA8AXHiAAAAAXNSR0IArs4c6QAAAzBJREFUeF7t3DFRQ0EARdGfBg9owAQlWEEDJV6QACUCaNEQF4AA6JjLMLwTAZvs3TOvzOni7vHj+KOf59uH4/ry7dd/3c3T/fFyvvr17/1PX3gC6+tzgvVz4mB90xAssH5eAKykocUCC6ykAFhJVosFFlhJAbCSrBYLLLCSAmAlWS0WWGAlBcBKslossMBKCoCVZLVYYIGVFAAryWqxwAIrKQBWktVigQVWUgCsJKvFAguspABYSVaLBRZYSQGwkqwWCyywkgJgJVktFlhgJQXASrJaLLDASgqAlWS1WGCBlRQAK8lqscACKykAVpLVYoEFVlIArCSrxQILrKQAWElWiwUWWEkBsJKsFgsssJICYCVZLRZYYCUFwEqyWiywwEoKgJVktVhggZUUACvJarHAAispAFaS1WKBBVZSAKwkq8UCC6ykAFhJVosFFlhJAbCSrBYLLLCSAmAlWS0WWGAlBcBKsp7eX4+P5GSHThcAa/r5u8uD1bWdPhms6efvLg9W13b6ZLCmn7+7PFhd2+mTwZp+/u7yYHVtp08Ga/r5u8uD1bWdPhms6efvLg9W13b6ZLCmn7+7PFhd2+mTwZp+/u7yYHVtp08Ga/r5u8uD1bWdPhms6efvLg9W13b6ZLCmn7+7PFhd2+mTwZp+/u7yYHVtp08Ga/r5u8uD1bWdPhms6efvLg9W13b6ZLCmn7+7vP/H+qbtzdP98XK+6qoPnAwWWAlzsMACKykAVpLVYoEFVlIArCSrxQILrKQAWElWiwUWWEkBsJKsFgsssJICYCVZLRZYYCUFwEqyWiywwEoKgJVktVhggZUUACvJarHAAispAFaS1WKBBVZSAKwkq8UCC6ykAFhJVosFFlhJAbCSrBYLLLCSAmAlWS0WWGAlBcBKslossMBKCoCVZLVYYIGVFAAryWqxwAIrKQBWktVigQVWUgCsJKvFAguspABYSVaLBRZYSQGwkqwWCyywkgJgJVktFlhgJQXASrJaLLDASgqAlWS1WGCBlRQAK8lqscACKykAVpLVYoEFVlIArCSrxQILrKQAWElWiwUWWEkBsJKsFgsssJICYCVZPwEmqB6xMz/znQAAAABJRU5ErkJggg=="
                      alt="Sweden | سوئد" title="ُSweden flag | پرچم سوئد " class="flag"></th>
                  <th><img
                      src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAdCAMAAADfG89gAAAAHlBMVEX/AAD1AQH////9SEj9PDz9Ly/9EhL8ICD19fX9tLRcGnLKAAAACXBIWXMAAAsTAAALEwEAmpwYAAAGsWlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNy4wLWMwMDAgNzkuMjE3YmNhNiwgMjAyMS8wNi8xNC0xODoyODoxMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0RXZ0PSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VFdmVudCMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIDIyLjQgKFdpbmRvd3MpIiB4bXA6Q3JlYXRlRGF0ZT0iMjAyMS0xMC0yN1QyMDo1NzowOSswMzozMCIgeG1wOk1vZGlmeURhdGU9IjIwMjEtMTAtMjdUMjE6MDE6NTQrMDM6MzAiIHhtcDpNZXRhZGF0YURhdGU9IjIwMjEtMTAtMjdUMjE6MDE6NTQrMDM6MzAiIGRjOmZvcm1hdD0iaW1hZ2UvcG5nIiBwaG90b3Nob3A6Q29sb3JNb2RlPSIyIiBwaG90b3Nob3A6SUNDUHJvZmlsZT0ic1JHQiBJRUM2MTk2Ni0yLjEiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6ZmFhOGU2ZTUtZDhkNS0zNTQ2LWE4OWItNTg4MmE2MWQ0NDk1IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjY0ODU2ZjhjLWE3YzUtNTE0ZC1hOTRiLWY1NjM5YzZhNTgzMyIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOjY0ODU2ZjhjLWE3YzUtNTE0ZC1hOTRiLWY1NjM5YzZhNTgzMyI+IDx4bXBNTTpIaXN0b3J5PiA8cmRmOlNlcT4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNyZWF0ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6NjQ4NTZmOGMtYTdjNS01MTRkLWE5NGItZjU2MzljNmE1ODMzIiBzdEV2dDp3aGVuPSIyMDIxLTEwLTI3VDIwOjU3OjA5KzAzOjMwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjIuNCAoV2luZG93cykiLz4gPHJkZjpsaSBzdEV2dDphY3Rpb249InNhdmVkIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOjk1NWNlNjcwLWRlY2UtYzE0YS1iOWY3LTA3ZDQ3NTg2ZDZkNyIgc3RFdnQ6d2hlbj0iMjAyMS0xMC0yN1QyMTowMToxOCswMzozMCIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWRvYmUgUGhvdG9zaG9wIDIyLjQgKFdpbmRvd3MpIiBzdEV2dDpjaGFuZ2VkPSIvIi8+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJzYXZlZCIgc3RFdnQ6aW5zdGFuY2VJRD0ieG1wLmlpZDpmYWE4ZTZlNS1kOGQ1LTM1NDYtYTg5Yi01ODgyYTYxZDQ0OTUiIHN0RXZ0OndoZW49IjIwMjEtMTAtMjdUMjE6MDE6NTQrMDM6MzAiIHN0RXZ0OnNvZnR3YXJlQWdlbnQ9IkFkb2JlIFBob3Rvc2hvcCAyMi40IChXaW5kb3dzKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8L3JkZjpTZXE+IDwveG1wTU06SGlzdG9yeT4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4GgurEAAABZ0lEQVQ4jZ2U626lMAyEvwk9Prz/225oMvvDIVCg1W4TCZT4Mp6xFfHzWu5XJX9C6Gp7uBpZDovmHmc/gOzAzygj4mxQhgj5oSaZIqQjYzr+iMKCAGUCyQjpG5S1v/v7k2ISBo8CZR1MDwayFBU++m6R00Hgncu1pneNFn1nsNcu/EXPO0rytIzAo77M4t1f3neBx84DFDAe6MZkDPHsnDuhPMpl1apVVKAtbWmtlZF6xEzwKUCPGjVaNKDRogVyUX66wLcZe9eoUWkA0aLSsp/ZFejlaONgPuGijt/rxsia3GU5iOReIQgqgKsrNnbKfFM4saJ+iGOQtA+Nbyi5E8VDx41tP2AK8lQ4vcc5a9KG9cL1D1ulbt5Ow+/zOAsaL7YlHTZEt5bunhofCk+9SylrgUAxJ0EohOKL/1xRSilFoaHe8Sgc4RfpTyni5D0b8IAyLkNxNv8ryoXkNeL2jj3l+iXK/66/14ecXPHVwOgAAAAASUVORK5CYII="
                      alt="Swiss | سوییس" title="ُSwiss flag | پرچم سوییس " class="flag"></th>
                </tr>

                <tr class="country-fa">
                  <td>آمریکا,</td>
                  <td>انگلیس,</td>
                  <td>اسپانیا</td>
                  <td>هلند</td>
                  <td>فرانسه,</td>
                  <td>سوئد,</td>
                  <td>&nbspسوییس</td>
                </tr>

                <tr class="country-en">
                  <td>USA</td>
                  <td>,England&nbsp</td>
                  <td>,Spain&nbsp</td>
                  <td>,Netherlands&nbsp</td>
                  <td>,Swiss&nbsp</td>
                  <td>,Sweden&nbsp</td>
                  <td>,France&nbsp</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </Section>

    <Section class="english-content" title="English Content">
      <div class="container-fluid text-lighter">
        <div class="row justify-content-center align-items-center text-center">
          <div class="col-auto">
            <h2>
              Access to Betcart from the following countries is not possible.
              <br><br>
              If you are using a VPN, please change the country setting on your VPN and then refresh the page.
            </h2>
          </div>
        </div>
      </div>
    </Section>
   </Section>
