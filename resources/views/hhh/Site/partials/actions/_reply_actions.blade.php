@php
    // Like data
    $showLike = false;
    if (isset($replyActions['like'])) {
        $showLike = true;
        $like = $replyActions['like'];
        $likesCount = $like['count'];
        $isUserLiked = $like['isUserLiked'];
        $likableViewId = $like['likableViewId'];
        $onSubmitLike = $like['onSubmit'];
    }
@endphp

@php
    // Comment data
    $showComment = false;
    /* if (isset($replyActions['comment'])) {
        $showComment = true;
        $comment = $replyActions['comment'];
        $commentCount = $comment['count'];
        $commentableViewId = $comment['commentableViewId'];
        $onSubmitComment = $comment['onSubmit'];
    } */
@endphp

<div class="comment-actions">

    <div class="d-flex flex-nowrap mb-2">
        @if ($showLike)
            <button type="button" class="btn btn-icon" onclick="{!! $onSubmitLike !!}">
                <i id="{{ $likableViewId }}_icon"
                    class="fa-regular @if ($isUserLiked) fa-solid liked @endif  fa-heart"></i>

                <span id="{{ $likableViewId }}_counter">{{ $likesCount }}</span>
            </button>
        @endif
        @if ($showComment)
            <button type="button" class="btn btn-icon">
                <i class="fa-regular fa-comment"></i>
                <span id="{{ $commentableViewId }}_counter">{{ $commentCount }}</span>
            </button>
            {{-- <a type="button" class="btn btn-icon" href="#{{ $commentableViewId }}_comment"
                onclick="toggleElementClass('{{ $commentableViewId }}_comment_section', 'd-none');">
                <i class="fa-regular fa-comment"></i>
                <span id="{{ $commentableViewId }}_counter">{{ $commentCount }}</span>
            </a> --}}
        @endif
    </div>
</div>
