@php
    $sidebarController = new App\Http\Controllers\Site\SidebarController();
    $TableEnum = App\Enums\Database\Tables\PostGroupsTableEnum::class;

    $idCol = $TableEnum::Id->dbName();
    $titleCol = $TableEnum::Title->dbName();
    $isSpaceCol = $TableEnum::IsSpace->dbName();
    $displayUrl = 'display_url';
@endphp

<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        {{-- Features --}}

        {{-- Features END --}}

        {{-- Site Content --}}

        {{-- Root Groups --}}
        @foreach ($sidebarController->getRootGroups() as $rootGroup)
            {{-- Root Group Item --}}
            <li class="nav-item nav-category">{{ $rootGroup[$titleCol] }}</li>

            {{-- Subsets of root group --}}
            @foreach ($sidebarController->getGroupSubsets($rootGroup[$idCol]) as $rootSubset)
                @if ($rootSubset[$isSpaceCol])
                    {{-- Space Item --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $rootSubset[$displayUrl] }}" title="{{ $rootSubset[$titleCol] }}">
                            {{-- <i class="menu-icon"></i> --}}
                            <span class="menu-title">{{ $rootSubset[$titleCol] }}</span>
                        </a>
                    </li>
                @else
                    {{-- Category Item --}}
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-sidebarItem-{{ $rootSubset[$idCol] }}"
                            aria-expanded="false" aria-controls="#ui-sidebarItem-{{ $rootSubset[$idCol] }}">
                            <span class="menu-title">{{ $rootSubset[$titleCol] }}</span>
                            <i class="menu-arrow"></i>
                        </a>

                        <div class="collapse" id="ui-sidebarItem-{{ $rootSubset[$idCol] }}">
                            <ul class="nav flex-column sub-menu">

                                {{-- Category Subsets --}}
                                @foreach ($sidebarController->getGroupSubsets($rootSubset[$idCol]) as $categorySubset)
                                    {{-- Sub category Item --}}
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ $categorySubset[$displayUrl] }}"
                                            title="{{ $categorySubset[$titleCol] }}">{{ $categorySubset[$titleCol] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endif
            @endforeach
            {{-- Subsets of root group END --}}
        @endforeach
        {{-- Root Groups END --}}

        {{-- Site Content END --}}

        {{-- Only Super Admin --}}
        @if (Auth::Check() && Auth::user()->isSuperAdmin())
            {{-- Language --}}
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#ui-language" aria-expanded="false"
                    aria-controls="ui-language">
                    <i class="mdi mdi-cube-outline menu-icon"></i>
                    <span class="menu-title">@lang('general.Language')</span>
                    <i class="menu-arrow"></i>
                </a>

                <div class="collapse" id="ui-language">
                    <ul class="nav flex-column sub-menu">

                        @foreach (config('app.available_locales') as $locale)
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ SitePublicRoutesEnum::Locale->route($locale) }}">@lang('general.locale.LangName.' . $locale)</a>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </li>
            {{-- Language END --}}
        @endif
        {{-- Only Super Admin END --}}
    </ul>
</nav>
