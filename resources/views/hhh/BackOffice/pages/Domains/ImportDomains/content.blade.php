<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                @php $attrName = "DomainsListInput"; @endphp
                @include('hhh.widgets.form.input-text_area-field', [
                    'attrName' => $attrName,
                    'label' => trans('PagesContent_ImportDomains.form.' . $attrName . '.name'),
                    'notice' => trans('PagesContent_ImportDomains.form.' . $attrName . '.notice'),
                    'placeholder' => trans('PagesContent_ImportDomains.form.' . $attrName . '.placeholder'),
                    'value' => null,
                    'rows' => 15,
                    'style' => 'resize:vertical; direction: ltr;',
                ])

                {{-- DomainCategoryId --}}
                @php $attrName = "domain_category_id"; @endphp
                @include('hhh.widgets.form.dropdown', [
                    'attrName' => $attrName,
                    'label' => trans('PagesContent_ImportDomains.form.' . $attrName . '.name'),
                    'notice' => trans('PagesContent_ImportDomains.form.' . $attrName . '.notice'),
                    'placeholder' => trans('PagesContent_ImportDomains.form.' . $attrName . '.placeholder'),
                    'collection' => $domainCategoriesCollection,
                    'selectedItem' => null,
                ])

                {{-- DomainHolderAccountId --}}
                @php $attrName = "domain_holder_account_id"; @endphp
                @include('hhh.widgets.form.dropdown', [
                    'attrName' => $attrName,
                    'label' => trans('PagesContent_ImportDomains.form.' . $attrName . '.name'),
                    'notice' => trans('PagesContent_ImportDomains.form.' . $attrName . '.notice'),
                    'placeholder' => trans('PagesContent_ImportDomains.form.' . $attrName . '.placeholder'),
                    'collection' => $domainHolderAccountsCollection,
                    'selectedItem' => null,
                ])

                {{-- Overwrite --}}
                @php $attrName = "Overwrite"; @endphp
                @include('hhh.widgets.form.switch-btn', [
                    'attrName' => $attrName,
                    'label' => trans('PagesContent_ImportDomains.form.' . $attrName . '.name'),
                    'notice' => trans('PagesContent_ImportDomains.form.' . $attrName . '.notice'),
                    'value' => false,
                ])

                <button id="importBtn" type="button" class="btn btn-primary btn-icon-text font-weight-bold"
                    onclick="domainsImporter.importData();">
                    <i class="fa-solid fa-floppy-disk btn-icon-prepend"></i>@lang('general.Save')
                </button>

                <div id="progressbarContainer" class="progress progress-lg mt-2 w-100 d-none">
                    <div id="progressbar" class="progress-bar bg-success" role="progressbar" style="width: 60%"
                        aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">60%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body" style="overflow-x: scroll; min-height: 400px;">

                @php
                    $cellClass = 'px-2 text-center';
                @endphp
                <table id="DataDispalyTable" class="table mb-0">
                    <thead>
                        <tr>
                            <th class="{{ $cellClass }}">@lang('general.Row')</th>
                            <th class="{{ $cellClass }}">@lang('general.Name')</th>
                            <th class="{{ $cellClass }}">@lang('general.Status')</th>
                            <th class="{{ $cellClass }}">@lang('thisApp.AdminPages.Domains.autoRenew')</th>
                            <th class="{{ $cellClass }}">@lang('thisApp.AdminPages.Domains.registeredAt')</th>
                            <th class="{{ $cellClass }}">@lang('thisApp.AdminPages.Domains.expiresAt')</th>
                            <th class="{{ $cellClass }}">@lang('thisApp.AdminPages.Domains.announcedAt')</th>
                            <th class="{{ $cellClass }}">@lang('thisApp.AdminPages.Domains.blockedAt')</th>
                            <th class="{{ $cellClass }}">@lang('general.Description')</th>
                            <th class="{{ $cellClass }}">@lang('general.Result')</th>
                        </tr>
                    </thead>

                </table>

            </div>
        </div>
    </div>
</div>


{{-- These items are used to create a new view by JavaScript. --}}
<Section name="templates" class="d-none">

    @include('hhh.BackOffice.pages.Domains.ImportDomains.templates.TableDataRow')

</Section>

{{-- Translated Texts --}}
<Section name="translattins" class="d-none">
    <input type="hidden" id="ConfirmImportTitle" value="@lang('PagesContent_ImportDomains.messages.ConfirmImportTitle')">
    <input type="hidden" id="ConfirmImportMsg" value="@lang('PagesContent_ImportDomains.messages.ConfirmImportMsg')">
    <input type="hidden" id="NoDataMsg" value="@lang('PagesContent_ImportDomains.messages.NoDataMsg')">
</Section>
