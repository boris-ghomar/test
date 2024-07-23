<div id="export_to_excel_block" class="d-inline-block p-2">
    <iframe id="iframeDownloadFile" src="" style="display:none;width:100%;"></iframe>

    <b>@lang('general.export.exportTo.exportTo')</b>
    <button id="btnExportExcel" type="button" onclick='jsGridCtrl.exportExcelService();'
        class="btn btn-icon-text btn-inverse-success btn-fw" title="@lang('general.export.exportTo.excel')">
        <i class="fa-solid fa-download btn-icon-prepend"></i>Excel
    </button>
</div>
