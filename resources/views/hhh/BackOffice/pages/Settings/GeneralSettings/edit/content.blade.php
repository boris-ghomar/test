<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                @include('hhh.widgets.messages.ShowFormResultMessages')

                <h4 class="card-title">@lang('PagesContent_GeneralSettings.cardTitle')</h4>
                <p class="card-description text-justify">@lang('PagesContent_GeneralSettings.cardDescription')</p>

                {{-- tablist --}}
                <ul class="nav nav-tabs" role="tablist">

                    {{-- admin_panel-tab --}}
                    <li class="nav-item">
                        <a class="nav-link active" id="admin_panel-tab" data-bs-toggle="tab" href="#admin_panel"
                            role="tab" aria-controls="admin_panel" aria-selected="true"><i
                                class="far fa-building"></i>
                            @lang('PagesContent_GeneralSettings.tab.AdminPanel.title')</a>
                    </li>

                    {{-- community-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="community-tab" data-bs-toggle="tab" href="#community" role="tab"
                            aria-controls="community" aria-selected="false"><i class="fa-solid fa-people-group"></i>
                            @lang('PagesContent_GeneralSettings.tab.Community.title')</a>
                    </li>

                    {{-- CommunityRegistration-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="community_registration-tab" data-bs-toggle="tab"
                            href="#community_registration" role="tab" aria-controls="community_registration"
                            aria-selected="false"><i class="fa-solid fa-user-plus"></i>
                            @lang('PagesContent_GeneralSettings.tab.CommunityRegistration.title')</a>
                    </li>

                    {{-- CommunityPasswordRecovery-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="community_password_recovery-tab" data-bs-toggle="tab"
                            href="#community_password_recovery" role="tab"
                            aria-controls="community_password_recovery" aria-selected="false"><i
                                class="fa-solid fa-user-magnifying-glass"></i>
                            @lang('PagesContent_GeneralSettings.tab.CommunityPasswordRecovery.title')</a>
                    </li>

                    {{-- Chatbot-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="chatbot-tab" data-bs-toggle="tab" href="#chatbot" role="tab"
                            aria-controls="chatbot" aria-selected="false"><i class="fa-solid fa-message-bot"></i>
                            @lang('PagesContent_GeneralSettings.tab.Chatbot.title')</a>
                    </li>

                    {{-- Ticket-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="ticket-tab" data-bs-toggle="tab" href="#ticket" role="tab"
                            aria-controls="ticket" aria-selected="false"><i class="fa-solid fa-message-question"></i>
                            @lang('PagesContent_GeneralSettings.tab.Ticket.title')</a>
                    </li>

                    {{-- Referral-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="referral-tab" data-bs-toggle="tab" href="#referral" role="tab"
                            aria-controls="referral" aria-selected="false">
                            <i class="fa-duotone fa-person-sign"></i>
                            @lang('PagesContent_GeneralSettings.tab.Referral.title')</a>
                    </li>

                    {{-- Bet-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="bet-tab" data-bs-toggle="tab" href="#bet" role="tab"
                            aria-controls="bet" aria-selected="false">
                            <i class="fa-solid fa-money-check-dollar"></i>
                            @lang('PagesContent_GeneralSettings.tab.Bet.title')</a>
                    </li>

                    {{-- app_rules-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="app_rules-tab" data-bs-toggle="tab" href="#app_rules" role="tab"
                            aria-controls="app_rules" aria-selected="false"><i class="far fa-balance-scale"></i>
                            @lang('PagesContent_GeneralSettings.tab.AppRules.title')</a>
                    </li>

                </ul>
                {{-- tablist END --}}

                {{-- tab-content --}}
                <div class="tab-content">

                    {{-- AdminPanel --}}
                    <div class="tab-pane fade active show" id="admin_panel" role="tabpanel"
                        aria-labelledby="admin_panel-tab">
                        @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.TabsContent.AdminPanel')
                    </div>

                    {{-- Community --}}
                    <div class="tab-pane fade" id="community" role="tabpanel" aria-labelledby="community-tab">
                        @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.TabsContent.Community')
                    </div>

                    {{-- CommunityRegistration --}}
                    <div class="tab-pane fade" id="community_registration" role="tabpanel"
                        aria-labelledby="community_registration-tab">
                        @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.TabsContent.CommunityRegistration')
                    </div>

                    {{-- CommunityPasswordRecovery --}}
                    <div class="tab-pane fade" id="community_password_recovery" role="tabpanel"
                        aria-labelledby="community_password_recovery-tab">
                        @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.TabsContent.CommunityPasswordRecovery')
                    </div>

                    {{-- Chatbot --}}
                    <div class="tab-pane fade" id="chatbot" role="tabpanel" aria-labelledby="chatbot-tab">
                        @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.TabsContent.Chatbot')
                    </div>

                    {{-- Ticket --}}
                    <div class="tab-pane fade" id="ticket" role="tabpanel" aria-labelledby="ticket-tab">
                        @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.TabsContent.Ticket')
                    </div>

                    {{-- Referral --}}
                    <div class="tab-pane fade" id="referral" role="tabpanel" aria-labelledby="referral-tab">
                        @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.TabsContent.Referral')
                    </div>

                    {{-- Bet --}}
                    <div class="tab-pane fade" id="bet" role="tabpanel" aria-labelledby="bet-tab">
                        @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.TabsContent.Bet')
                    </div>

                    {{-- AppRules --}}
                    <div class="tab-pane fade" id="app_rules" role="tabpanel" aria-labelledby="app_rules-tab">
                        @include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.TabsContent.AppRules')
                    </div>

                </div>
                {{-- tab-content END --}}
            </div>
        </div>
    </div>
</div>
