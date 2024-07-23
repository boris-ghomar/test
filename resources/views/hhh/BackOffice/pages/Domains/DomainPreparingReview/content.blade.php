<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body">


                <div id="NoDomainMsg" class="d-none">
                    @lang('PagesContent_DomainPreparingReview.messages.NoDomainMsg')
                </div>

                <div id="DomainCheckSection" class="d-none">

                    <h5 class="mb-3">
                        <p class="text-white-75">@lang('general.Domain'): <br><span id="domainNameDisplay"></span></p>
                        <p>@lang('PagesContent_DomainPreparingReview.remainingDomainsCount'): <span id="remainingDomainsCount"></span></p>
                    </h5>

                    <div class="d-flex flex-row justify-content-center justify-content-between">

                        <div class="me-2 d-flex flex-column justify-content-center justify-items-center"
                            style="width:300px;height:600px">

                            <span class="mb-2 w-100 text-center">@lang('PagesContent_DomainPreparingReview.MobileView')</span>

                            <iframe id="iframDomainCheckMobile" src=""
                                style="width:100%;height:100%;margin-top: 10px;"
                                onload="domainPreparingReviewer.iframeLoaded('mobile');"></iframe>

                            <div id="mobileCheckButtonsSection" class="mt-4 d-flex justify-content-center d-none">
                                <button id="btnMobileSucccess" type="button"
                                    class="btn btn-success btn-icon-text text-white"
                                    onclick="domainPreparingReviewer.loadResult('mobile',true);"><i
                                        class="fa-solid fa-check btn-icon-prepend"></i>@lang('PagesContent_DomainPreparingReview.btnLoadOk')</button>

                                <button id="btnMobileFailed" type="button"
                                    class="btn btn-danger btn-icon-text text-white ms-2"
                                    onclick="domainPreparingReviewer.loadResult('mobile',false);"><i
                                        class="fa-solid fa-xmark btn-icon-prepend"></i>@lang('PagesContent_DomainPreparingReview.btnLoadFailed')</button>
                            </div>
                        </div>

                        <div class="d-flex flex-column justify-content-center justify-items-center"
                            style="width:900px;height:600px;">

                            <span class="mb-2 w-100 text-center">@lang('PagesContent_DomainPreparingReview.DesktopView')</span>

                            <iframe id="iframDomainCheckDesktop" src=""
                                style="width:100%;height:100%;margin-top: 10px;"
                                onload="domainPreparingReviewer.iframeLoaded('desktop');"></iframe>

                            <div id="desktopCheckButtonsSection" class="mt-4 d-flex justify-content-center d-none">
                                <button id="btnDesktopSucccess" type="button"
                                    class="btn btn-success btn-icon-text text-white "
                                    onclick="domainPreparingReviewer.loadResult('desktop',true);"><i
                                        class="fa-solid fa-check btn-icon-prepend"></i>@lang('PagesContent_DomainPreparingReview.btnLoadOk')</button>

                                <button id="btnDesktopFailed" type="button"
                                    class="btn btn-danger btn-icon-text text-white ms-2"
                                    onclick="domainPreparingReviewer.loadResult('desktop',false);"><i
                                        class="fa-solid fa-xmark btn-icon-prepend"></i>@lang('PagesContent_DomainPreparingReview.btnLoadFailed')</button>
                            </div>

                        </div>
                    </div>

                    <div id="submitSection" class="mt-5 d-none">

                        {{-- AdminPanelTimeZone --}}
                        @php $attrName = "descr"; @endphp
                        @include('hhh.widgets.form.input-text_area-field', [
                            'attrName' => $attrName,
                            'label' => trans('PagesContent_DomainPreparingReview.form.' . $attrName . '.name'),
                            'notice' => trans('PagesContent_DomainPreparingReview.form.' . $attrName . '.notice'),
                            'placeholder' => trans(
                                'PagesContent_DomainPreparingReview.form.' . $attrName . '.placeholder'),
                            'value' => null,
                            'rows' => 3,
                            'style' => 'resize:vertical;',
                        ])

                        <button type="button" class="btn btn-primary btn-icon-text text-white"
                            onclick="domainPreparingReviewer.submit();"><i
                                class="fa-solid fa-clipboard-check btn-icon-prepend"></i>@lang('general.buttons.submit')</button>

                        <button type="button" class="btn btn-success btn-icon-text text-white"
                            onclick="domainPreparingReviewer.refreshDisplays();"><i
                                class="fa-solid fa-arrows-rotate btn-icon-prepend"></i>@lang('general.buttons.Refresh')</button>

                        <button type="button" class="btn btn-success btn-icon-text text-white"
                            onclick="domainPreparingReviewer.moveBackwardStep();"><i
                                class="fa-solid fa-backward-step btn-icon-prepend"></i>@lang('general.buttons.back')</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


{{-- Translated Texts --}}
<Section name="translattins" class="d-none">
    <input type="hidden" id="UnknownMobileLoadResultMsg" value="@lang('PagesContent_DomainPreparingReview.messages.UnknownMobileLoadResultMsg')">
    <input type="hidden" id="UnknownDesktopLoadResultMsg" value="@lang('PagesContent_DomainPreparingReview.messages.UnknownDesktopLoadResultMsg')">
</Section>
