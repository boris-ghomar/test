<div class="col-lg-{{ $containerCssCols }} grid-margin stretch-card">
    <div class="card card-rounded">
        <div class="card-body chartjs">

            <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                    @if (!empty($cardTitle))
                        <h4 class="card-title card-title-dash">{!! $cardTitle !!}</h4>
                    @endif
                    @if (!empty($cardSubtitle))
                        <h5 class="card-subtitle card-subtitle-dash">{!! $cardSubtitle !!}</h5>
                    @endif
                </div>
                <div id="{{ $containerId }}-legend">
                </div>
            </div>
            <div class="chartjs-wrapper mt-4">
                <canvas id="{{ $containerId }}" width="448" height="150"
                    style="display: block; box-sizing: border-box; height: 150px; width: 448px;"></canvas>
            </div>

            @if (!empty($cardFooter))
                <small class="footer-text text-muted">{!! $cardFooter !!}</small>
            @endif
        </div>
    </div>
</div>
