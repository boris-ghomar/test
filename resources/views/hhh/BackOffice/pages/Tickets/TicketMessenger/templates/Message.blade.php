{{--
    Below items will be replaced by javascript

    MessageTemplate_Style1

    MessageTemplate_isUserMessage
    MessageTemplate_MessageType
    MessageTemplate_MessageView
    MessageTemplate_MessageTime
 --}}
<div id="MessageTemplate">
    <div class="row chat-message MessageTemplate_Style1">
        <div class="card" data-is-user-message="MessageTemplate_isUserMessage"
            data-msg-type="MessageTemplate_MessageType">
            <div class="card-body">

                MessageTemplate_MessageView

                @if (Auth::user()->isPersonnel())
                    <div class="ltr d-flex flex-row pt-3 msg-time">
                        <small>MessageTemplate_MessageTime</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
