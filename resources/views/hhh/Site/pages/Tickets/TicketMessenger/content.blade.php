<div id="ticketMessageContainer" class="messenger messages-container pb-3"></div>

<div class="messenger">
    <div id="new_message_section" class="d-none chat-new-message form-group d-block col-12" style="">
        <div class="input-group">


            <textarea class="form-control form-control-lg border-0" rows="5" placeholder="@lang('thisApp.placeholder.message')" type="text"
                id="new_message_text" required></textarea>

            <button type="button" class="btn me-0 p-3" onclick="ticketMessenger.sendMessage(document.getElementById('new_message_text'));">
                <i class="fa-solid fa-send btn-icon-prepend align-items-center"></i>
            </button>

            <button type="button" class="btn btn-icon me-0 p-2"
                onclick="document.getElementById('upload_file').click();">
                <i class="fa-solid fa-paperclip"></i>
            </button>

        </div>
    </div>
</div>

{{-- These items are used to create a new view by JavaScript. --}}
<Section name="templates" class="d-none">

    @include('hhh.BackOffice.pages.Tickets.TicketMessenger.templates.Loading')
    @include('hhh.BackOffice.pages.Tickets.TicketMessenger.templates.Message')
    @include('hhh.BackOffice.pages.Tickets.TicketMessenger.templates.Profiles')

</Section>

{{-- File Container Input --}}
<input type="file" id="upload_file" class="d-none" accept=".jpg,.png"
    onchange="ticketMessenger.sendMessage(this);">

{{-- Translated Texts --}}
{{-- <input type="hidden" id="ChatbotMessenger_ImageRemoved" value="@lang('thisApp.Errors.ChatbotMessenger.imageRemoved')"> --}}
