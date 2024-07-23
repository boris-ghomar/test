@php
    // Like data
    $showLike = false;
    if (isset($postActions['like'])) {
        $showLike = true;
        $like = $postActions['like'];
        $likesCount = $like['count'];
        $isUserLiked = $like['isUserLiked'];
        $likableViewId = $like['likableViewId'];
        $onSubmitLike = $like['onSubmit'];
    }
@endphp

@php
    // Comment data
    $showComment = false;
    if (isset($postActions['comment'])) {
        $showComment = true;
        $comment = $postActions['comment'];
        $commentCount = $comment['count'];
        $commentableViewId = $comment['commentableViewId'];
        $onSubmitComment = $comment['onSubmit'];
    }
@endphp

<div class="post-actions">

    <div class="d-flex flex-nowrap mb-2">
        @if ($showLike)
            <button type="button" class="btn btn-icon" onclick="{!! $onSubmitLike !!}">
                <i id="{{ $likableViewId }}_icon"
                    class="fa-regular @if ($isUserLiked) fa-solid liked @endif  fa-heart"></i>

                <span id="{{ $likableViewId }}_counter">{{ $likesCount }}</span>
            </button>
        @endif
        @if ($showComment)
            <button type="button" class="btn btn-icon"
                onclick="toggleElementClass('{{ $commentableViewId }}_comment_section', 'd-none');">
                <i class="fa-regular fa-comment"></i>
                <span id="{{ $commentableViewId }}_counter">{{ $commentCount }}</span>
            </button>
        @endif

        @if ($canEditPost)
            <div class="btn btn-icon" style="cursor: default;">
                <i class="fa-regular fa-eye"></i>
                <span>{{ number_format($post->views) }}</span>
            </div>
            <a target="_blank" href="{{ $editLink }}" class="btn btn-icon" title="@lang('general.buttons.Edit')"><i
                    class="fa-regular fa-money-check-pen"></i></a>
        @endif

    </div>

    @if ($showComment)
        <div id="{{ $commentableViewId }}_comment_section" class="form-group d-block">
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
