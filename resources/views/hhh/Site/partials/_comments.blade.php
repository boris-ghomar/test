@if (!empty($postComments))

    <div class="row grid-margin">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title ms-2 mt-2">@lang('thisApp.Comments')</h4>

                    @foreach ($postComments as $postComment)
                        @php
                            // Comment self data
                            $postCommentActions = $postComment['actions'];
                            $data = $postComment['data'];
                            $text = $data['text'];
                            $commentViewId = $data['htmlViewId'];
                            $profilePhoto = $data['profilePhoto'];
                            $displayName = $data['displayName'];
                            $replies = $data['replies'];
                        @endphp
                        @php
                            // Comment action data
                            $showComment = false;
                            if (isset($postCommentActions['comment'])) {
                                $showComment = true;
                                $comment = $postCommentActions['comment'];
                                $commentCount = $comment['count'];
                                $commentableViewId = $comment['commentableViewId'];
                                $onSubmitComment = $comment['onSubmit'];
                            }
                        @endphp


                        <div id="{{ $commentViewId }}_anchor_link"
                            class="d-flex flex-column @if (!$loop->last) border-bottom @endif pt-2 pb-2">

                            <div class="comment-box">
                                <div class="d-flex pb-0">
                                    <img class="img-sm rounded-circle" src="{{ $profilePhoto }}" alt="profile">
                                    <div class="ms-3 w-100">
                                        <h6 class="text-capitalize mb-1 ms-2">{{ $displayName }}</h6>
                                        @include('hhh.Site.partials.actions._comment_actions')
                                    </div>
                                </div>
                                <small class="text-muted">{{ $text }}</small>
                            </div>
                            @foreach ($replies as $reply)
                                @include('hhh.Site.partials.actions._comment_reply', $reply)
                            @endforeach

                            @if ($showComment)
                                <a type="button" class="btn text-start"
                                    onclick="this.classList.toggle('d-none');toggleElementClass('{{ $commentableViewId }}_comment_section', 'd-none');">@lang('thisApp.placeholder.comment')</a>

                                <div id="{{ $commentableViewId }}_comment_section"
                                    class="d-none form-group d-block mt-2 mb-3">
                                    <div class="input-group">
                                        <textarea class="form-control form-control-lg border-0" rows="5" placeholder="@lang('thisApp.placeholder.comment')" type="text"
                                            name="{{ $commentableViewId }}_comment" id="{{ $commentableViewId }}_comment" required></textarea>

                                    </div>

                                    <button type="button" class="btn btn-primary btn-icon-text float-end mt-2 me-0"
                                        onclick="{!! $onSubmitComment !!}">
                                        <i class="fa-solid fa-send btn-icon-prepend align-items-center"></i>
                                        @lang('thisApp.Buttons.Send')
                                    </button>
                                </div>
                            @endif


                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
