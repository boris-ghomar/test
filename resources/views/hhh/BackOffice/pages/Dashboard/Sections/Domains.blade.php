@if (!is_null($domainSection))

    @php
        $assignableDomainsStatistics = $domainSection['assignableDomainsStatistics'];
    @endphp
    <div class="row domain">

        {{-- Domains Statistics --}}
        <div class="col-md-12 grid-margin">
            <div class="card d-flex">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <i class="fa-brands fa-internet-explorer icon-md"></i>
                        <div class="ms-3 w-100">

                            <h6>@lang('thisApp.AdminPages.Dashboard.Domains.AssignableDomainsStatistics')</h6>

                            <div class="col-md-12">
                                <div class="statistics-details d-flex align-items-center justify-content-between">

                                    @foreach ($assignableDomainsStatistics as $title => $value)
                                        <div>
                                            <p class="statistics-title">{{ $title }}</p>
                                            <h3 class="statistics-value">{{ $value }}</h3>
                                        </div>
                                    @endforeach

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Domains Statistics END --}}

    </div>


@endif
