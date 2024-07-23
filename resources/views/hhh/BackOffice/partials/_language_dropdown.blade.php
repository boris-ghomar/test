@if (\App\Enums\Routes\RouteTypesEnum::isAdminRoute())

    <div class="btn-group mx-auto me-3">
        <button type="button"
            class="btn btn-primary pe-none">{{ __('general.locale.LangName.' . config('app.locale')) }}</button>
        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="languageDropdownMenu"
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

        </button>
        <div class="dropdown-menu" aria-labelledby="languageDropdownMenu" style="">

            @foreach (config('app.available_locales') as $locale)
                <a class="dropdown-item" href="{{ App\Enums\Routes\AdminPublicRoutesEnum::Locale->route($locale) }}">
                    <i class="flag-icon flag-icon-{{ __('general.locale.Alpha-2_Code', [], $locale) }} mx-1"></i>
                    @lang('general.locale.LangName.' . $locale)
                </a>
                @if (!$loop->last)
                    <div class="dropdown-divider"></div>
                @endif
            @endforeach

        </div>
    </div>
@endif
