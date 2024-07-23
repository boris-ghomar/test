@include('hhh.BackOffice.pages.Posts.ArticlePosts.Create.form-start', ['tabpanel' => 'Content'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-file-pen"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_PostForm.tab.Content.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_PostForm.tab.Content.descriptionText')</p>
    </div>
</div>

{{-- PostSpaceId --}}
@php $attrName = $PostsTableEnum::PostSpaceId->dbName(); @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_PostForm.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PostForm.form.' . $attrName . '.notice'),
    'collection' => $postSpaceCollection,
    'selectedItem' => old($attrName),
])

{{-- Title --}}
@php $attrName = $PostsTableEnum::Title->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_PostForm.form.' . $attrName . '.name'),
    'notice' => trans('general.SeoMetaTags.MetaTitle.Description'),
    'placeholder' => trans('PagesContent_PostForm.form.' . $attrName . '.placeholder'),
    'value' => old($attrName),
])
<span style="display:block; position: relative;top:-20px;font-size:12px;">@lang('general.LengthOf', ['attribute' => trans('PagesContent_PostForm.form.' . $attrName . '.name')]) <i
        id="title_length"></i></span>


{{-- Form END --}}
@include('hhh.BackOffice.pages.Posts.ArticlePosts.Create.form-end')
