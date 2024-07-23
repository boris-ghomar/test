<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                @include('hhh.widgets.messages.ShowFormResultMessages')

                <h4 class="card-title">@lang('PagesContent_TechnicalSettings.cardTitle')</h4>
                <p class="card-description text-justify">@lang('PagesContent_TechnicalSettings.cardDescription')</p>

                {{-- tablist --}}
                <ul class="nav nav-tabs" role="tablist">

                    {{-- betconstruct_external_admin-tab --}}
                    <li class="nav-item">
                        <a class="nav-link active" id="betconstruct_external_admin-tab" data-bs-toggle="tab"
                            href="#betconstruct_external_admin" role="tab"
                            aria-controls="betconstruct_external_admin" aria-selected="true">
                            <i class="far fa-webhook"></i>
                            @lang('PagesContent_TechnicalSettings.tab.BetconstructExternalAdmin.title')</a>
                    </li>

                    {{-- betconstruct_swarm_api-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="betconstruct_swarm_api-tab" data-bs-toggle="tab"
                            href="#betconstruct_swarm_api" role="tab" aria-controls="betconstruct_swarm_api"
                            aria-selected="true"><i class="far fa-webhook"></i>
                            @lang('PagesContent_TechnicalSettings.tab.BetconstructSwarmApi.title')</a>
                    </li>

                    {{-- justcall_api-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="justcall_api-tab" data-bs-toggle="tab" href="#justcall_api"
                            role="tab" aria-controls="justcall_api" aria-selected="true"><i
                                class="far fa-webhook"></i>
                            @lang('PagesContent_TechnicalSettings.tab.JustCallApi.title')</a>
                    </li>

                    {{-- trust_score_system-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="trust_score_system-tab" data-bs-toggle="tab" href="#trust_score_system"
                            role="tab" aria-controls="trust_score_system" aria-selected="true"><i
                                class="far fa-badge-check"></i>
                            @lang('PagesContent_TechnicalSettings.tab.TrustScoreSystem.title')</a>
                    </li>

                    {{-- domains_assignment_system-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="domains_assignment_system-tab" data-bs-toggle="tab"
                            href="#domains_assignment_system" role="tab" aria-controls="domains_assignment_system"
                            aria-selected="true"><i class="far fa-brands fa-internet-explorer"></i>
                            @lang('PagesContent_TechnicalSettings.tab.DomainsAssignment.title')</a>
                    </li>

                </ul>
                {{-- tablist END --}}

                {{-- tab-content --}}
                <div class="tab-content">

                    {{-- betconstruct_external_admin --}}
                    <div class="tab-pane fade active show" id="betconstruct_external_admin" role="tabpanel"
                        aria-labelledby="betconstruct_external_admin-tab">
                        @include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.TabsContent.BetconstructExternalAdmin')
                    </div>

                    {{-- betconstruct_swarm_api --}}
                    <div class="tab-pane fade" id="betconstruct_swarm_api" role="tabpanel"
                        aria-labelledby="betconstruct_swarm_api-tab">
                        @include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.TabsContent.BetconstructSwarmApi')
                    </div>

                    {{-- justcall_api-tab --}}
                    <div class="tab-pane fade" id="justcall_api" role="tabpanel" aria-labelledby="justcall_api-tab">
                        @include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.TabsContent.JustCallApi')
                    </div>

                    {{-- trust_score_system --}}
                    <div class="tab-pane fade" id="trust_score_system" role="tabpanel"
                        aria-labelledby="trust_score_system-tab">
                        @include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.TabsContent.TrustScoreSystem')
                    </div>

                    {{-- domains_assignment_system --}}
                    <div class="tab-pane fade" id="domains_assignment_system" role="tabpanel"
                        aria-labelledby="domains_assignment_system-tab">
                        @include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.TabsContent.DomainsAssignment')
                    </div>

                </div>
                {{-- tab-content END --}}
            </div>
        </div>
    </div>
</div>
