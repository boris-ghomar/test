<div class="row flex-grow">

    <div class="col-lg-7 mx-auto ">


        <div class="row mt-5 @lang('general.locale.direction')" style='font-family:"Manrope-regular"'>
            <div class="col-12 text-center mt-xl-2">

                <div class="brand-logo mb-5">
                    <img src="{{ App\Enums\Settings\AppSettingsEnum::CommunityBigLogo->getImageUrl() }}">
                </div>

                <p class="display-5 text-white-75 font-weight-medium">
                    @lang('thisApp.Errors.Forbidden')
                </p>
            </div>
        </div>

    </div>

    {{-- @include('hhh.BackOffice.partials._footer') --}}
</div>
