@php
    // Reply data
    $replyAnswerActions = $replyAnswer['actions'];
    $replyAnswerData = $replyAnswer['data'];
    $replyAnswerText = $replyAnswerData['text'];
    $replyAnswerViewId = $replyAnswerData['htmlViewId'];
    $replyAnswerProfilePhoto = $replyAnswerData['profilePhoto'];
    $replyAnswerDisplayName = $replyAnswerData['displayName'];
@endphp

<div id="{{ $replyAnswerViewId }}_anchor_link" class="ms-3">
    <div class="col-md-12 stretch-card">
        <div class="card comment-box">
            <div class="card-body pb-0">

                <div class="d-flex flex-column @if (!$loop->last) border-bottom @endif pt-2 pb-2">

                    <div class="d-flex pb-0">
                        <img class="img-sm rounded-circle" src="{{ $replyAnswerProfilePhoto }}" alt="profile">
                        <div class="ms-3">
                            <h6 class="text-capitalize mb-1 ms-2">{{ $replyAnswerDisplayName }}</h6>
                            @include('hhh.Site.partials.actions._reply_answer_actions')
                        </div>
                    </div>
                    <small class="text-muted">{{ $replyAnswerText }}</small>
                </div>

            </div>
        </div>
    </div>
</div>
