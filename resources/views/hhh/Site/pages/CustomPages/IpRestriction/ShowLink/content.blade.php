<div class="container-fluid page-body-wrapper full-page-wrapper"
    style="max-height: 70vh !important; min-height: 70vh !important;">

    <div class="content-wrapper d-flex align-items-center text-center">

        <div class="row flex-grow">

            <div class="col-12 mx-auto">

                <div class="brand-logo mb-5">
                    <img class="brand-logo-errorpage"
                        src="{{ App\Enums\Settings\AppSettingsEnum::CommunityBigLogo->getImageUrl() }}">
                </div>

                <div class="row mt-5 @lang('general.locale.direction')" style='font-family:"Manrope-regular"'>
                    <div class="col-12 text-center mt-xl-2 w-100">
                        <div class="display-4 mb-0 font-weight-medium">{!! $explanation !!}</div>

                        <br />
                        <a type="button" class="btn btn-primary btn-icon-text font-weight-bold mt-3"
                            href="{{ $siteLink }}" target="_blank">
                            {{-- <i class="mdi mdi-logout btn-icon-prepend"></i> --}}
                            @lang('thisApp.Buttons.GoToSite')
                        </a>
                    </div>
                </div>
                <div class="row mt-5 @lang('general.locale.direction')" style='font-family:"Manrope-regular"'>
                    <div class="col-12 text-center mt-xl-2 w-100">
                        <div class="display-4 mb-0 font-weight-medium">@lang('thisApp.CustomPages.IpRestriction.UnsupportedIPDirectLink')</div>
                        <br />
                        <div class="display-5 mt-3 mb-0 font-weight-medium ltr">{{ $siteLink }}</div>

                        <br />
                        <button type="button" class="btn btn-success btn-icon-text font-weight-bold mt-3"
                            onclick="copyUrl('{{ $siteLink }}');">
                            {{-- <i class="mdi mdi-logout btn-icon-prepend"></i> --}}
                            @lang('thisApp.CustomPages.IpRestriction.CopySiteURL')
                        </button>

                        <div id="site_url_copied" class="display-5 mt-3 mb-0 font-weight-medium d-none">
                            @lang('thisApp.CustomPages.IpRestriction.SiteURLCopied')</div>
                    </div>
                </div>



            </div>

        </div>

    </div>
</div>
