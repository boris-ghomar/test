@php
    $domain_bcUnblockedDomain = $domainSection['bcUnblockedDomain'];
    $domain_bcPermenantDomain = $domainSection['bcPermenantDomain'];
    $domain_domainId = $domainSection['domainId'];
    $domain_showReportBtn = $domainSection['showReportBtn'];
    $domain_isDomainReported = $domainSection['isDomainReported'];

    $telegramBot_joinLink = $telegramBotSection['joinLink'];
@endphp

{{-- Note --}}
@php
    $communityDashboradNoteTitle = AppSettingsEnum::CommunityDashboradNoteTitle->getValue();
    $communityDashboradNoteText = AppSettingsEnum::CommunityDashboradNoteText->getValue();
@endphp
@if (AppSettingsEnum::IsCommunityDashboradNoteActive->getValue() &&
        !(empty($communityDashboradNoteTitle) && empty($communityDashboradNoteText)))
    <div class="row">

        <div class="col-md-12 grid-margin note">
            <div class="card d-flex align-items-start">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-start">
                        <i class="fa-solid fa-star icon-md"></i>
                        <div class="ms-3">
                            <div class="mb-2">{!! $communityDashboradNoteTitle !!}</div>
                            <div class="text-muted text-justify">{!! $communityDashboradNoteText !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endif
{{-- Note END --}}


{{-- Domain --}}
<div class="row">

    {{-- Unblocked Domain --}}
    <div class="col-md-6 grid-margin domain">
        <div class="card d-flex align-items-start">
            <div class="card-body">
                <div class="d-flex flex-row align-items-start">
                    <i class="fa-duotone fa-browser icon-md"></i>
                    <div class="ms-3">

                        <h6>@lang('thisApp.Site.Dashboard.BcUnblockedUrl')</h6>
                        <a id="domain_bcUnblockedUrlView" href="" target="_blank"></a>
                        <button type="button" class="btn btn-outline-primary btn-icon-text mt-3"
                            onclick="domainCtl.copySiteUrlToClipboard('domain_bcUnblockedUrlView');">
                            <i class="fa-solid fa-clipboard"></i>
                            @lang('general.buttons.CopyUrl')
                        </button>

                        @if ($domain_showReportBtn)
                            <button id="btnDomainReport" type="button"
                                class="btn btn-outline-warning btn-icon-text mt-3"
                                onclick="domainCtl.reportDomainIssue();">
                                <i class="fa-solid fa-flag"></i>
                                @lang('thisApp.Buttons.ReportIssue')
                            </button>
                        @endif

                    </div>
                </div>
                <p id="domainMsgView" class="mt-2">
                    @if ($domain_isDomainReported)
                        @lang('thisApp.Site.Dashboard.msg.DomainReported')
                    @endif
                </p>
            </div>
        </div>
    </div>
    {{-- Unblocked Domain END --}}

    {{-- Permenant Domain --}}
    <div class="col-md-6 grid-margin domain">
        <div class="card d-flex align-items-start">
            <div class="card-body">
                <div class="d-flex flex-row align-items-start">
                    <i class="fa-duotone fa-browser icon-md"></i>
                    <div class="ms-3">

                        <h6>@lang('thisApp.Site.Dashboard.BcPermenantUrl')</h6>
                        <a id="domain_bcPermenantUrlView" href="" target="_blank"></a>
                        <button type="button" class="btn btn-outline-primary btn-icon-text mt-3"
                            onclick="domainCtl.copySiteUrlToClipboard('domain_bcPermenantUrlView');">
                            <i class="fa-solid fa-clipboard"></i>
                            @lang('general.buttons.CopyUrl')
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Permenant Domain END --}}

</div>

{{-- Data --}}
<input type="hidden" id="domainIdView" value="{{ $domain_domainId }}">
<input type="hidden" id="domain_bcPermenantDomainView" value="{{ $domain_bcPermenantDomain }}">
<input type="hidden" id="domain_bcUnblockedDomainView" value="{{ $domain_bcUnblockedDomain }}">

{{-- Translated Texts --}}
<input type="hidden" id="DomainCopiedMsg" value="@lang('thisApp.Site.Dashboard.msg.DomainCopied')">
<input type="hidden" id="DomainReportConfirmMsg" value="@lang('thisApp.Site.Dashboard.msg.DomainReportConfirm')">

{{-- Domain END --}}

{{-- Telegram Bot --}}
<div class="row">

    {{-- Telegram join bot --}}
    <div class="col-md-6 grid-margin telegram">
        <div class="card d-flex align-items-start">
            <div class="card-body">
                <div class="d-flex flex-row align-items-start">
                    <i class="fa-brands fa-telegram icon-md"></i>
                    <div class="ms-3">

                        <h6>@lang('thisApp.Site.Dashboard.telegramBotJoinLink.title')</h6>
                        <p class="text-muted">@lang('thisApp.Site.Dashboard.telegramBotJoinLink.descr')</p>
                        <a type="button" class="btn btn-outline-primary btn-icon-text mt-3"
                            href="{{ $telegramBot_joinLink }}" target="_blank">
                            <i class="fa-brands fa-telegram icon-md"></i>
                            @lang('thisApp.Site.Dashboard.telegramBotJoinLink.joinBtn')
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Telegram join bot END --}}

    {{-- Referral --}}
    @can('viewAny', App\Models\Site\Referral\ReferralPanel::class)
        <div class="col-md-6 grid-margin referral">
            <div class="card d-flex align-items-start">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-start">
                        <i class="fa-duotone fa-person-sign icon-md"></i>
                        <div class="ms-3">

                            <h6>@lang('thisApp.Site.Dashboard.Referral.title')</h6>
                            <p class="text-muted">@lang('thisApp.Site.Dashboard.Referral.descr')</p>
                            <a type="button" class="btn btn-outline-primary btn-icon-text mt-3"
                                href="{{ SiteRoutesEnum::Referral_Panel->url() }}" target="_parent">
                                <i class="fa-solid fa-right-to-bracket icon-md"></i>
                                <span class="align-top">@lang('thisApp.Site.Dashboard.Referral.btn')</span>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    {{-- Referral END --}}

</div>
{{-- Telegram Bot END --}}
