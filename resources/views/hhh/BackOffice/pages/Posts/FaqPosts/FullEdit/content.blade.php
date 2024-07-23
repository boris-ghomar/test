<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                @include('hhh.widgets.messages.ShowFormResultMessages')

                <h4 class="card-title">@lang('PagesContent_PostForm.cardTitleEdit')</h4>
                <p class="card-description text-justify">@lang('PagesContent_PostForm.cardDescriptionEdit')</p>

                {{-- tablist --}}
                <ul class="nav nav-tabs" role="tablist">

                    {{-- Content-tab --}}
                    <li class="nav-item">
                        <a class="nav-link active" id="content-tab" data-bs-toggle="tab" href="#page-content"
                            role="tab" aria-controls="page-content" aria-selected="true"><i
                                class="far fa-file-pen"></i>
                            @lang('PagesContent_PostForm.tab.Content.title')</a>
                    </li>

                    {{-- SEO-tab --}}
                    <li class="nav-item">
                        <a class="nav-link" id="seo-tab" data-bs-toggle="tab" href="#page-seo" role="tab"
                            aria-controls="page-seo" aria-selected="false"><i class="far fa-brands fa-searchengin"></i>
                            @lang('PagesContent_PostForm.tab.SEO.title')</a>
                    </li>

                </ul>
                {{-- tablist END --}}

                {{-- tab-Content --}}
                <div class="tab-content">

                    {{-- Content --}}
                    <div class="tab-pane fade active show" id="page-content" role="tabpanel"
                        aria-labelledby="content-tab">
                        @include('hhh.BackOffice.pages.Posts.FaqPosts.FullEdit.TabsContent.Content')
                    </div>

                    {{-- SEO --}}
                    <div class="tab-pane fade" id="page-seo" role="tabpanel" aria-labelledby="seo-tab">
                        @include('hhh.BackOffice.pages.Posts.FaqPosts.FullEdit.TabsContent.SEO')
                    </div>

                </div>
                {{-- tab-content END --}}
            </div>
        </div>
    </div>
</div>
