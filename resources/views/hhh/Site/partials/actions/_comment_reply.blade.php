@php
    // Reply data
    $replyActions = $reply['actions'];
    $replyData = $reply['data'];
    $replyText = $replyData['text'];
    $replyViewId = $replyData['htmlViewId'];
    $replyProfilePhoto = $replyData['profilePhoto'];
    $replyDisplayName = $replyData['displayName'];

    $replyAnswers = $replyData['replies'];
@endphp

<div id="{{ $replyViewId }}_anchor_link" class="ms-3">
    <div class="col-md-12 stretch-card">
        <div class="card comment-box">
            <div class="card-body pb-0">

                <div class="d-flex flex-column @if (!$loop->last) border-bottom @endif pt-2 pb-2">

                    <div class="d-flex pb-0">
                        <img class="img-sm rounded-circle" src="{{ $replyProfilePhoto }}" alt="profile">
                        <div class="ms-3">
                            <h6 class="text-capitalize mb-1 ms-2">{{ $replyDisplayName }}</h6>
                            @include('hhh.Site.partials.actions._reply_actions')
                        </div>
                    </div>
                    <small class="text-muted">{{ $replyText }}</small>

                    @foreach ($replyAnswers as $replyAnswer)
                        @include('hhh.Site.partials.actions._comment_reply_answer', $replyAnswer)
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>
