<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                @include('hhh.widgets.messages.ShowFormResultMessages')

                <h4 class="card-title">@lang('PagesContent_PersonnelProfile.cardTitle')</h4>
                <p class="card-description text-justify">@lang('PagesContent_PersonnelProfile.cardDescription')</p>

                {{-- tablist --}}
                <ul class="nav nav-tabs" role="tablist">

                    {{-- Personal-tab --}}
                    <li class="nav-item">
                        <a class="nav-link active" id="personal-tab" data-bs-toggle="tab" href="#personal" role="tab"
                            aria-controls="personal" aria-selected="false"><i class="far fa-passport"></i>
                            @lang('PagesContent_PersonnelProfile.tab.Personal.title')</a>
                    </li>

                    {{-- Photo-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="photo-tab" data-bs-toggle="tab" href="#photo" role="tab"
                            aria-controls="photo" aria-selected="false"><i class="far fa-image"></i>
                            @lang('PagesContent_PersonnelProfile.tab.Photo.title')</a>
                    </li>

                    {{-- Password-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab"
                            aria-controls="password" aria-selected="false"><i class="far fa-user-lock"></i>
                            @lang('PagesContent_PersonnelProfile.tab.Password.title')</a>
                    </li>

                    @if ($settingsTabDispaly)
                        {{-- Settings-tab --}}
                        <li class="nav-item">
                            <a class="nav-link" id="settings-tab" data-bs-toggle="tab" href="#settings" role="tab"
                                aria-controls="settings" aria-selected="false">
                                <i class="fa-regular fa-gear"></i>
                                @lang('PagesContent_PersonnelProfile.tab.Settings.title')</a>
                        </li>
                    @endif

                </ul>
                {{-- tablist END --}}

                {{-- tab-content --}}
                <div class="tab-content">

                    {{-- Personal --}}
                    <div class="tab-pane fade active show" id="personal" role="tabpanel"
                        aria-labelledby="personal-tab">
                        @include('hhh.BackOffice.pages.UserProfile.edit.TabsContent.Personal')
                    </div>

                    {{-- Photo --}}
                    <div class="tab-pane fade" id="photo" role="tabpanel" aria-labelledby="photo-tab">
                        @include('hhh.BackOffice.pages.UserProfile.edit.TabsContent.Photo')
                    </div>

                    {{-- Password --}}
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        @include('hhh.BackOffice.pages.UserProfile.edit.TabsContent.Password')
                    </div>

                    @if ($settingsTabDispaly)
                        {{-- Settings --}}
                        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                            @include('hhh.BackOffice.pages.UserProfile.edit.TabsContent.Settings')
                        </div>
                    @endif

                </div>
                {{-- tab-content END --}}
            </div>
        </div>
    </div>
</div>
