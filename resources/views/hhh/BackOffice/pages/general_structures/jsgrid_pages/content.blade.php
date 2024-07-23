<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                @if ($customizablePage ?? '')
                    @include('hhh.BackOffice.pages.general_structures.jsgrid_pages._CustomizePageBtn')
                @endif

                @if ($useExcelExport ?? '')
                    @include('hhh.BackOffice.pages.general_structures.jsgrid_pages._ExcelExport')
                @endif

                @if ($customizablePage ?? '')
                    @include('hhh.BackOffice.pages.general_structures.jsgrid_pages._CustomizePageSettings')
                @endif

                <div id="jsGrid"></div>
            </div>
        </div>
    </div>
</div>
