@include('hhh.BackOffice.pages.Posts.ArticlePosts.FullEdit.form-start', ['tabpanel' => 'MainPhoto'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-duotone fa-image"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_PostForm.tab.MainPhoto.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_PostForm.tab.MainPhoto.descriptionText')</p>
    </div>
</div>



{{-- MainPhoto --}}
@php $attrName = $PostsTableEnum::MainPhoto->dbName(); @endphp
@include('hhh.widgets.form.upload_photo', [
    'fileAssistant' => $mainPhotoFileAssistant,
    'attrName' => $attrName,
    'label' => trans('PagesContent_PostForm.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PostForm.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_PostForm.form.' . $attrName . '.placeholder'),
])

{{-- Form END --}}
@include('hhh.BackOffice.pages.Posts.ArticlePosts.FullEdit.form-end')
