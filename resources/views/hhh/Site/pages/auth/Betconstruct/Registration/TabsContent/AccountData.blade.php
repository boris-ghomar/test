@include('hhh.Site.pages.auth.Betconstruct.Registration.form-start', ['tabpanel' => 'AccountData'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-passport"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_RegisaterationBetconstruct.tab.AccountData.descriptionTitle')</h4>
        <p class="text-justify">
            @lang('PagesContent_RegisaterationBetconstruct.tab.AccountData.descriptionText')
        </p>
    </div>
</div>

{{-- login --}}
@php $attrName = $ClientExtrasTableEnum::Login->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.placeholder'),
    'value' => old($attrName),
    'style' => 'direction: ltr;',
])

{{-- password --}}
@php $attrName = "regPassword"; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.placeholder'),
    'value' => old($attrName),
    'style' => 'direction: ltr;',
])

{{-- first_name --}}
@php $attrName = $ClientExtrasTableEnum::FirstName->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.placeholder'),
    'value' => old($attrName),
])

{{-- last_name --}}
@php $attrName = $ClientExtrasTableEnum ::LastName->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.placeholder'),
    'value' => old($attrName),
])

{{-- currency_id --}}
@php $attrName = $ClientExtrasTableEnum::CurrencyId->dbName(); @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
    'collection' => $currenciesCollection,
    'selectedItem' => old($attrName, $defaultCurrency),
])

@include('hhh.Site.pages.auth.Betconstruct.Registration.form-end')
