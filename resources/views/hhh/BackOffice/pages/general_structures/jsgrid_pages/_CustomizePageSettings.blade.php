<div id="CustomizePageForm" class="d-block p-2 d-none">

    {{-- customizablePageSelectedColumns --}}
    @php $attrName = "customizablePageColumns" @endphp
    @include('hhh.widgets.form.checkbox_group', [
        'attrName' => $attrName,
        'label' => trans('general.CustomizablePage.title'),
        'notice' => trans('general.CustomizablePage.description'),
        'collection' => $customizablePageSettings[config('hhh_config.keywords.selectableColumns')],
        'selectedItemsList' => $customizablePageSettings[config('hhh_config.keywords.selectedColumns')],
        'collapse' => true,
        'useSelectButtons' => true,
    ])

    <button type="submit" class="btn btn-gradient-primary mr-2 mb-3"
        onclick="jsGridCtrl.updateCustomizeTableSettings();">@lang('general.buttons.save')</button>
</div>
