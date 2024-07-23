
{{-- this is smaple of modal box html code and used in "modalBox.js->getModalStructure()" --}}

{{-- The Modal --}}
<div id='{{ $modalId }}' class='hhh_modal'>

    {{-- Modal content --}}
    <div class='hhh_modal-content' id='modal_content_{{ $modalId }}' >

      <div class='hhh_modal-header'>
            <div class='close-hhh_modal' >
                <span id='close_{{ $modalId }}'>&times;</span>
            </div>
            <h4  id='modal_header_{{ $modalId }}'></h4>
      </div>

      <div id='modal_body_{{ $modalId }}' class='hhh_modal-body'>
      </div>

      <div id='modal_footer_{{ $modalId }}' class='hhh_modal-footer'>
          <input id="btn_custom_{{ $modalId }}" class='btn btn-inverse-dark' type='button' value='Custom Button' />
          <input id="btn_realized_{{ $modalId }}" class='btn btn-inverse-dark' type='button' value='@lang('general.I_realized')' />
          <input id="btn_yes_{{ $modalId }}" class='btn btn-inverse-dark' type='button' value='@lang('general.YES')' />
          <input id="btn_no_{{ $modalId }}" class='btn btn-inverse-dark' type='button' value='@lang('general.NO')' />
      </div>

    </div>


</div>
