@php
    $controller = App\Http\Controllers\Site\Auth\Betconstruct\RegisterBetconstructController::class;
@endphp
<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @php
                    $oldTabPanel = old('_tabpanel');
                    if (!empty($oldTabPanel)) {
                        $oldTabPanel = Str::snake($oldTabPanel) . '_tab';
                    }
                @endphp

                <div class="d-flex justify-content-center auth px-0">
                    <div class="brand-logo">
                        <a href="{{ SitePublicRoutesEnum::MainPage->url() }}">
                            <img style="width: 250px;" src="{{ AppSettingsEnum::CommunityBigLogo->getImageUrl() }}">
                        </a>
                    </div>
                </div>

                @include('hhh.widgets.messages.ShowFormResultMessages', [
                    'resultDisplayId' => $oldTabPanel,
                ])

                <h4 class="card-title">@lang('PagesContent_RegisaterationBetconstruct.cardTitle')</h4>
                <p class="card-description text-justify">@lang('PagesContent_RegisaterationBetconstruct.cardDescription')</p>

                {{-- Steps Progressbar --}}
                <div class="d-flex flex-row flex-wrap justify-content-between mb-3">

                    @foreach ($stepsProgressList as $stepName => $stepProgressData)
                        <div class="me-2 @if ($stepName == $tabPanel) text-white-75 @endif">

                            <p class="@if ($stepProgressData['isPassed']) text-success @endif">
                                <i class="{{ $stepProgressData['icon'] }}"></i>

                                {{ $stepProgressData['displayName'] }}

                                @if ($stepProgressData['isPassed'])
                                    <i class="fa-solid fa-circle-check"></i>
                                @else
                                    @if ($stepName == $tabPanel)
                                        <i class="fa-regular fa-circle-half-stroke"></i>
                                    @else
                                        <i class="fa-regular fa-circle"></i>
                                    @endif
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
                {{-- tablist --}}
                <ul class="nav nav-tabs" role="tablist">

                    {{-- GetMobileNumber-tab --}}
                    {{-- @if ($tabPanel == 'GetMobileNumber1')
                        <li class="nav-item">
                            <a class="nav-link active" id="mobile_verification-tab" data-bs-toggle="tab"
                                href="#mobile_verification" role="tab" aria-controls="mobile_verification"
                                aria-selected="false"><i class="fa-solid fa-mobile-screen-button"></i>
                                @lang('PagesContent_RegisaterationBetconstruct.tab.GetMobileNumber.title')</a>
                        </li>
                    @endif --}}


                </ul>
                {{-- tablist END --}}

                {{-- tab-content --}}
                <div class="tab-content">

                    {{-- GetMobileNumber --}}
                    @if ($tabPanel == 'GetMobileNumber')
                        <div class="tab-pane fade active show" id="get_mobile_number" role="tabpanel"
                            aria-labelledby="get_mobile_number-tab">
                            @include('hhh.Site.pages.auth.Betconstruct.Registration.TabsContent.GetMobileNumber')
                        </div>
                    @endif

                    {{-- VerifyMobileNumber --}}
                    @if ($tabPanel == 'VerifyMobileNumber')
                        <div class="tab-pane fade active show" id="verify_mobile_number" role="tabpanel"
                            aria-labelledby="verify_mobile_number-tab">
                            @include('hhh.Site.pages.auth.Betconstruct.Registration.TabsContent.VerifyMobileNumber')
                        </div>
                    @endif

                    {{-- GetEmail --}}
                    @if ($tabPanel == 'GetEmail')
                        <div class="tab-pane fade active show" id="get_email" role="tabpanel"
                            aria-labelledby="get_email-tab">
                            @include('hhh.Site.pages.auth.Betconstruct.Registration.TabsContent.GetEmail')
                        </div>
                    @endif

                    {{-- VerifyEmail --}}
                    @if ($tabPanel == 'VerifyEmail')
                        <div class="tab-pane fade active show" id="verify_email" role="tabpanel"
                            aria-labelledby="verify_email-tab">
                            @include('hhh.Site.pages.auth.Betconstruct.Registration.TabsContent.VerifyEmail')
                        </div>
                    @endif

                    {{-- AccountData --}}
                    @if ($tabPanel == 'AccountData')
                        <div class="tab-pane fade active show" id="account_data" role="tabpanel"
                            aria-labelledby="account_data-tab">
                            @include('hhh.Site.pages.auth.Betconstruct.Registration.TabsContent.AccountData')
                        </div>
                    @endif

                    {{-- FurtherInformation --}}
                    @if ($tabPanel == 'FurtherInformation')
                        <div class="tab-pane fade active show" id="further_information" role="tabpanel"
                            aria-labelledby="further_information-tab">
                            @include('hhh.Site.pages.auth.Betconstruct.Registration.TabsContent.FurtherInformation')
                        </div>
                    @endif

                </div>

                <div class="my-4 d-flex">
                    <a class="auth-link text-gray" href="{{ SitePublicRoutesEnum::defaultLogin()->route() }}">
                        @lang('auth_site.custom.ForgotPasswordForm.ReturnToLoginPage')
                    </a>
                </div>
                {{-- tab-content END --}}
            </div>
        </div>
    </div>
</div>
