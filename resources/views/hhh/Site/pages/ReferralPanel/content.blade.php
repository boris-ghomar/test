{{-- Note --}}
@php
    $referralPageNote = AppSettingsEnum::ReferralPageNote->getValue();
@endphp
@if (!empty($referralPageNote))
    <div class="row">

        <div class="col-md-12 grid-margin note">
            <div class="card d-flex align-items-start">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-start">
                        <div class="ms-3">
                            <div class="text-muted text-justify text-enter">{!! $referralPageNote !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endif
{{-- Note END --}}


{{-- 1th row --}}
<div class="row">

    {{-- Referral Link --}}
    <div class="col-md-12 grid-margin referral-link">
        <div class="card d-flex align-items-start">
            <div class="card-body">
                <div class="d-flex flex-row align-items-start">
                    <i class="fa-duotone fa-link icon-md"></i>
                    <div class="ms-3">

                        <h6>@lang('thisApp.Site.ReferralPanel.ReferralLink')</h6>
                        <p class="text-muted">@lang('thisApp.Site.ReferralPanel.ReferralLinkNote')</p>
                        <div id="referral_link" class="link">{{ $referralLink }}</div>
                        <button type="button" class="btn btn-outline-primary btn-icon-text mt-3"
                            onclick="copyReferralLink('{{ $referralLink }}');">
                            <i class="fa-solid fa-clipboard"></i>
                            @lang('general.buttons.CopyUrl')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Referral Link END --}}

</div>

{{-- 1th row END --}}

{{-- 2th row  --}}
<div class="row">
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-danger card-img-holder text-white">
            <div class="card-body">
                <img src="/assets/site/template/images/dashboard/circle.svg" class="card-img-absolute"
                    alt="circle-image">
                <h5 class="font-weight-normal mb-3">@lang('thisApp.Site.ReferralPanel.Statistics.AllReferralsCount')<i class="fa-solid fa-users float-end"></i>
                </h5>
                <h2 class="mb-5">{{ $allReferralsCount }}</h2>
                <h6 class="card-text">{{ $allReferralsChange }}</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-info card-img-holder text-white">
            <div class="card-body">
                <img src="/assets/site/template/images/dashboard/circle.svg" class="card-img-absolute"
                    alt="circle-image">
                <h4 class="font-weight-normal mb-3">@lang('thisApp.Site.ReferralPanel.Statistics.ActiveReferralsCount')<i class="fa-solid fa-user-check float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $activeReferralsCount }}</h2>
                <h6 class="card-text">{{ $activeReferralsChange }}</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-success card-img-holder text-white">
            <div class="card-body">
                <img src="/assets/site/template/images/dashboard/circle.svg" class="card-img-absolute"
                    alt="circle-image">
                <h4 class="font-weight-normal mb-3">@lang('thisApp.Site.ReferralPanel.Statistics.TotalReward')<i
                        class="mdi mdi-chart-line mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalReward }}</h2>
                <h6 class="card-text">{{ $totalRewardChange }}</h6>
            </div>
        </div>
    </div>
</div>
{{-- 2th row  END --}}

{{-- 3th row  --}}
<div class="row flex-grow">
    {!! $referredPerformanceChartView !!}
</div>
{{-- 3th row  END --}}

{{-- 4th row  --}}
<div class="row flex-grow">
    {!! $rewardPerformanceChartView !!}
</div>
{{-- 4th row  END --}}

{{-- 5th row  --}}

<div class="row flex-grow">

    {{-- Inprogress Session --}}
    @if ($showInProgressSessionCard)
        <div class="col-md-12 grid-margin inprogress-session">
            <div class="card d-flex align-items-start">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-start">
                        <i class="fa-solid fa-arrow-progress icon-md"></i>
                        <div class="ms-3">

                            <h6>@lang('thisApp.Site.ReferralPanel.InprogressSession.CardTitle')</h6>
                            <p class="text-muted">@lang('thisApp.Site.ReferralPanel.InprogressSession.CardSubtitle')</p>

                            <ul>
                                {{-- Timing --}}
                                <h6>@lang('thisApp.Site.ReferralPanel.InprogressSession.Timing')</h6>
                                <li>@lang('thisApp.Site.ReferralPanel.InprogressSession.RewardCalculationStartTime') {{ $inProgressSessionStartedAt }}</li>
                                <li>@lang('thisApp.Site.ReferralPanel.InprogressSession.RewardCalculationEndTime') {{ $inProgressSessionFinishedAt }}</li>

                                {{-- TermsAndConditions Referrer --}}
                                <h6>@lang('thisApp.Site.ReferralPanel.InprogressSession.TermsAndConditionsReferrer')</h6>
                                <li>@lang('thisApp.Site.ReferralPanel.InprogressSession.MinBetsCount') {{ $minBetCountReferrer }}</li>
                                <li>@lang('thisApp.Site.ReferralPanel.InprogressSession.MinBetOdds') {{ $minBetOddsReferrer }}</li>
                                <li>@lang('thisApp.Site.ReferralPanel.InprogressSession.MinBetAmount') {{ $minBetAmountReferrer }}</li>
                                <li>@lang('thisApp.Site.ReferralPanel.InprogressSession.BetResult')</li>

                                {{-- TermsAndConditions Referred --}}
                                <h6>@lang('thisApp.Site.ReferralPanel.InprogressSession.TermsAndConditionsReferred')</h6>
                                <li>@lang('thisApp.Site.ReferralPanel.InprogressSession.MinBetsCount') {{ $minBetCountReferred }}</li>
                                <li>@lang('thisApp.Site.ReferralPanel.InprogressSession.MinBetOdds') {{ $minBetOddsReferred }}</li>
                                <li>@lang('thisApp.Site.ReferralPanel.InprogressSession.MinBetAmount') {{ $minBetAmountReferred }}</li>
                                <li>@lang('thisApp.Site.ReferralPanel.InprogressSession.BetResult')</li>

                                {{-- BetsExceptions --}}
                                <h6>@lang('thisApp.Site.ReferralPanel.InprogressSession.BetsExceptionsTitle')</h6>
                                @foreach (__('thisApp.Site.ReferralPanel.InprogressSession.BetsExceptionItems') as $key => $value)
                                    <li>{{ $value }}</li>
                                @endforeach

                                {{-- InProgressReward --}}
                                @php
                                    $isRewaredClaimable = $inprogressReferralRewardItems['isClaimable'];
                                @endphp

                                @if (!empty($inprogressReferralRewardItems['collection']))
                                    <div class="">
                                        @if ($isRewaredClaimable)
                                            <form class="forms-sample"
                                                action="{{ route(SitePublicRoutesEnum::Referral_ClaimReward->value) }}"
                                                method="POST" onsubmit="modal_loading.show();">

                                                @csrf
                                                <input type="hidden" name="_method" value="PUT">
                                        @endif

                                        <h6>@lang('thisApp.Site.ReferralPanel.InprogressSession.InProgressReward.title')</h6>

                                        @include('hhh.widgets.form.checkbox_group', [
                                            'attrName' => 'claimedRewards',
                                            'label' => trans(
                                                'thisApp.Site.ReferralPanel.InprogressSession.RewardAmountLable'),
                                            'notice' => $isRewaredClaimable
                                                ? trans(
                                                    'thisApp.Site.ReferralPanel.InprogressSession.InProgressReward.claimableDescr',
                                                    ['claimableCount' => $claimableRewardsCount]
                                        )
                                                : null,
                                            'collection' => $inprogressReferralRewardItems['collection'],
                                            'selectedItemsList' => $inprogressReferralRewardItems['selectedItems'],
                                            'collapse' => false,
                                            'useSelectButtons' => false,
                                            'disabled' => !$isRewaredClaimable,
                                        ])
                                        @if ($isRewaredClaimable)
                                            <button type="submit"
                                                class="btn btn-gradient-primary mr-2 mb-3">@lang('general.buttons.save')</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- Inprogress Session END --}}

</div>
{{-- 5th row  END --}}


{{-- Translated Texts --}}
<input type="hidden" id="ReferralLinkCopiedMsg" value="@lang('thisApp.Site.ReferralPanel.msg.ReferralLinkCopied')">
