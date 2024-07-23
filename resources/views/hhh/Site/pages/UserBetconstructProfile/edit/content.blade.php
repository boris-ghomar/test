<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @php
                    $oldTabPanel = old('_tabpanel');
                    if (!empty($oldTabPanel)) {
                        $oldTabPanel = Str::snake($oldTabPanel).'_tab';
                    }
                @endphp
                @include('hhh.widgets.messages.ShowFormResultMessages', [
                    'resultDisplayId' => $oldTabPanel,
                ])

                <h4 class="card-title">@lang('PagesContent_UserBetconstructProfile.cardTitle')</h4>
                <p class="card-description text-justify">@lang('PagesContent_UserBetconstructProfile.cardDescription')</p>

                {{-- tablist --}}
                <ul class="nav nav-tabs" role="tablist">

                    {{-- Account-tab --}}
                    <li class="nav-item">
                        <a class="nav-link active" id="account-tab" data-bs-toggle="tab" href="#account" role="tab"
                            aria-controls="account" aria-selected="false"><i class="far fa-passport"></i>
                            @lang('PagesContent_UserBetconstructProfile.tab.Account.title')</a>
                    </li>

                    {{-- FurtherInformation-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="further_information-tab" data-bs-toggle="tab"
                            href="#further_information" role="tab" aria-controls="further_information"
                            aria-selected="false"><i class="fa-solid fa-square-info"></i>
                            @lang('PagesContent_UserBetconstructProfile.tab.FurtherInformation.title')
                            @if ($isFurtherInformationTabCompleted)
                            <i class="fa-solid fa-circle-check text-success"></i>@else<i
                                    id= 'further_information-incomplete'
                                    class="fa-solid fa-circle-exclamation text-danger"></i>
                            @endif
                        </a>
                    </li>

                    {{-- ChangeEmail-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="change_email-tab" data-bs-toggle="tab" href="#change_email"
                            role="tab" aria-controls="change_email" aria-selected="false"><i
                                class="fa-solid fa-envelope"></i>
                            @lang('PagesContent_UserBetconstructProfile.tab.ChangeEmail.title')
                            @if ($isEmailTabCompleted)
                            <i class="fa-solid fa-circle-check text-success"></i>@else<i
                                    id= 'change_email-incomplete'
                                    class="fa-solid fa-circle-exclamation text-danger"></i>
                            @endif
                        </a>
                    </li>

                    {{-- Password-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab"
                            aria-controls="password" aria-selected="false"><i class="far fa-user-lock"></i>
                            @lang('PagesContent_UserBetconstructProfile.tab.Password.title')</a>
                    </li>

                    {{-- Photo-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="photo-tab" data-bs-toggle="tab" href="#photo" role="tab"
                            aria-controls="photo" aria-selected="false"><i class="far fa-image"></i>
                            @lang('PagesContent_UserBetconstructProfile.tab.Photo.title')</a>
                    </li>

                    @if ($settingsTabDispaly)
                        {{-- Settings-tab --}}
                        <li class="nav-item">
                            <a class="nav-link" id="settings-tab" data-bs-toggle="tab" href="#settings" role="tab"
                                aria-controls="settings" aria-selected="false">
                                <i class="fa-regular fa-gear"></i>
                                @lang('PagesContent_UserBetconstructProfile.tab.Settings.title')</a>
                        </li>
                    @endif

                </ul>
                {{-- tablist END --}}

                {{-- tab-content --}}
                <div class="tab-content">

                    {{-- Account --}}
                    <div class="tab-pane fade active show" id="account" role="tabpanel" aria-labelledby="account-tab">
                        @include('hhh.Site.pages.UserBetconstructProfile.edit.TabsContent.Account')
                    </div>

                    {{-- FurtherInformation --}}
                    <div class="tab-pane fade" id="further_information" role="tabpanel"
                        aria-labelledby="further_information-tab">
                        @include('hhh.Site.pages.UserBetconstructProfile.edit.TabsContent.FurtherInformation')
                    </div>

                    {{-- ChangeEmail --}}
                    <div class="tab-pane fade" id="change_email" role="tabpanel" aria-labelledby="change_email-tab">
                        @include('hhh.Site.pages.UserBetconstructProfile.edit.TabsContent.ChangeEmail')
                    </div>

                    {{-- Password --}}
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        @include('hhh.Site.pages.UserBetconstructProfile.edit.TabsContent.Password')
                    </div>

                    {{-- Photo --}}
                    <div class="tab-pane fade" id="photo" role="tabpanel" aria-labelledby="photo-tab">
                        @include('hhh.Site.pages.UserBetconstructProfile.edit.TabsContent.Photo')
                    </div>

                    @if ($settingsTabDispaly)
                        {{-- Settings --}}
                        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                            @include('hhh.Site.pages.UserBetconstructProfile.edit.TabsContent.Settings')
                        </div>
                    @endif

                </div>
                {{-- tab-content END --}}
            </div>
        </div>
    </div>
</div>
