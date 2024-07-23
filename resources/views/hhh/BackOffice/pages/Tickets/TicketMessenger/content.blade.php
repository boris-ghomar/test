@php
    $TicketsTableEnum = App\Enums\Database\Tables\TicketsTableEnum::class;
    $ticketSubjectCol = $TicketsTableEnum::Subject->dbName();
    $ticketOwnerIdCol = $TicketsTableEnum::OwnerId->dbName();
    $ticketStatusCol = $TicketsTableEnum::Status->dbName();
    $ticketPrivateNoteCol = $TicketsTableEnum::PrivateNote->dbName();

    $ticketOwner = $ticket->owner;
    $ticketOwnerExtra = $ticketOwner->userExtra;
    $ClientModelEnum = App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum::class;
    $clientModelIdCol = $ClientModelEnum::Id->dbName();
    $clientModelCurrencyIdCol = $ClientModelEnum::CurrencyId->dbName();

    $RolesTableEnum = App\Enums\Database\Tables\RolesTableEnum::class;
    $roleNameCol = $RolesTableEnum::DisplayName->dbName();

@endphp

<div class="row">
    <div class="messenger-section">
        <div id="ticketMessageContainer" class="messenger messages-container pb-3"></div>

        <div class="messenger">
            <div id="new_message_section" class="d-none chat-new-message form-group d-block col-12" style="">
                <div class="input-group">


                    <textarea class="form-control form-control-lg border-0" rows="5" placeholder="@lang('thisApp.placeholder.message')" type="text"
                        id="new_message_text" required></textarea>

                    <button type="button" class="btn me-0 p-3"
                        onclick="ticketMessenger.sendMessage(document.getElementById('new_message_text'));">
                        <i class="fa-solid fa-send btn-icon-prepend align-items-center"></i>
                    </button>

                    <button type="button" class="btn btn-icon me-0 p-2"
                        onclick="document.getElementById('upload_file').click();">
                        <i class="fa-solid fa-paperclip"></i>
                    </button>

                </div>
            </div>
        </div>
    </div>

    {{-- messenger-sidebar --}}
    <div class="messenger-sidebar">

        <div class="row">

            <div class="data-row">
                <label>@lang('thisApp.Site.Tickets.Subject'):</label>
                <small class="data-value ps-1">{{ $ticket->$ticketSubjectCol }}</small>
            </div>

            <div class="data-row">
                <label>@lang('thisApp.AdminPages.Tickets.TicketID'):</label>
                <small class="data-value ps-1">{{ $ticket->id }}</small>
            </div>

            <div class="data-row">
                <label>@lang('thisApp.UserId'):</label>
                <small class="data-value ps-1">{{ $ticket->$ticketOwnerIdCol }}</small>
            </div>

            <div class="data-row">
                <label>@lang('thisApp.ClientCategory'):</label>
                <small class="data-value ps-1">{{ $ticketOwner->role->$roleNameCol }}</small>
            </div>

            @if (!is_null($ticketOwnerExtra))
                <div class="data-row">
                    <label>@lang('thisApp.BetconstructId'):</label>
                    <small class="data-value ps-1">{{ $ticketOwnerExtra->$clientModelIdCol }}</small>
                </div>

                <div class="data-row">
                    <label>@lang('bc_api.CurrencyId'):</label>
                    <small class="data-value ps-1">{{ $ticketOwnerExtra->$clientModelCurrencyIdCol }}</small>
                </div>
            @endif

            <div class="data-row d-flex flex-row align-items-center">
                <label>@lang('thisApp.Site.Tickets.Status'):</label>
                <select class="form-control ms-2" onchange="ticketMessenger.changeTicketStatus(this);">
                    @foreach ($ticketStatusCollection as $text => $value)
                        <option value='{{ $value }}' @if ($value == $ticket->$ticketStatusCol) selected @endif>
                            {{ $text }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="private_note-widget" class="form-group data-row">
                <label class="my-auto font-weight-bold" for="private_note">@lang('thisApp.PrivateNote'):</label>
                <div class="input-group ">
                    <textarea name="private_note" id="private_note" rows="5" class="form-control border-0 p-2" style="resize:vertical;"
                        placeholder="@lang('thisApp.PrivateNote')">{{ $ticket->$ticketPrivateNoteCol }}</textarea>
                </div>
            </div>

            <div class="data-row">
                <button class="btn btn-gradient-primary" onclick="ticketMessenger.submitForm();">@lang('general.buttons.save')</button>
            </div>

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
<input type="file" id="upload_file" class="d-none" accept=".jpg,.png" onchange="ticketMessenger.sendMessage(this);">

{{-- Translated Texts --}}
{{-- <input type="hidden" id="ChatbotMessenger_ImageRemoved" value="@lang('thisApp.Errors.ChatbotMessenger.imageRemoved')"> --}}
