@include('hhh.BackOffice.pages.Posts.FaqPosts.FullEdit.form-start', ['tabpanel' => 'SEO'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-brands fa-searchengin"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_PostForm.tab.SEO.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_PostForm.tab.SEO.descriptionText')</p>
    </div>
</div>

{{-- MetaDescription --}}
@php $attrName = $PostsTableEnum::MetaDescription->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('general.SeoMetaTags.MetaDescription.Title'),
    'notice' => trans('general.SeoMetaTags.MetaDescription.Description'),
    'placeholder' => trans('general.SeoMetaTags.MetaDescription.Title'),
    'value' => $itemData->$attrName,
])
<span style="display:block; position: relative;top:-20px;font-size:12px;">@lang('general.LengthOf', ['attribute' => trans('general.SeoMetaTags.MetaDescription.Title')]) <i
        @php $attrName = $PostsTableEnum::MetaDescription->dbName(); @endphp id="{{ $attrName . '_length' }}"></i></span>

@include('hhh.BackOffice.pages.Posts.FaqPosts.FullEdit.form-end')
