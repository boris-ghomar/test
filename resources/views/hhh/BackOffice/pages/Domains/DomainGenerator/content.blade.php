<div class="row grid-margin">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <form method="POST" action="{{ AdminRoutesEnum::Domains_DomainGenerator->route() }}">

                    @csrf

                    {{-- DomainCount --}}
                    @php $attrName = "DomainCount"; @endphp
                    @include('hhh.widgets.form.input-field', [
                        'type' => 'number',
                        'attrName' => $attrName,
                        'label' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.name'),
                        'notice' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.notice'),
                        'placeholder' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.placeholder'),
                        'value' => old($attrName, AppSettingsEnum::DomainGeneratorDomainCount->getValue()),
                        'style' => 'direction:ltr;',
                        'min' => 1,
                        'max' => 600,
                    ])

                    {{-- DomainLettersCount --}}
                    @php $attrName = "DomainLettersCount"; @endphp
                    @include('hhh.widgets.form.input-field', [
                        'type' => 'number',
                        'attrName' => $attrName,
                        'label' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.name'),
                        'notice' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.notice'),
                        'placeholder' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.placeholder'),
                        'value' => old($attrName, AppSettingsEnum::DomainGeneratorDomainLettersCount->getValue()),
                        'style' => 'direction:ltr;',
                        'min' => 7,
                        'max' => 30,
                    ])

                    {{-- ExcludeLetters --}}
                    @php $attrName = "ExcludeLetters"; @endphp
                    @include('hhh.widgets.form.input-field', [
                        'type' => 'text',
                        'attrName' => $attrName,
                        'label' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.name'),
                        'notice' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.notice'),
                        'placeholder' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.placeholder'),
                        'value' => old($attrName, AppSettingsEnum::DomainGeneratorExcludeLetters->getValue()),
                        'style' => 'direction:ltr;',
                    ])

                    {{-- DomainExtension --}}
                    @php $attrName = "DomainExtension"; @endphp
                    @include('hhh.widgets.form.dropdown', [
                        'attrName' => $attrName,
                        'label' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.name'),
                        'notice' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.notice'),
                        'placeholder' => trans('PagesContent_DomainGenerator.form.' . $attrName . '.placeholder'),
                        'collection' => $domainExtensionCollection,
                        'selectedItem' => old($attrName, AppSettingsEnum::DomainGeneratorDomainExtension->getValue()),
                    ])

                    <button type="submit" class="btn btn-primary btn-icon-text font-weight-bold">
                        <i class="fa-solid fa-gears btn-icon-prepend"></i>@lang('general.buttons.Generate')
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if (session('domains'))

    @php
        $domainsJson = session('domains');
        $domains = json_decode($domainsJson);
    @endphp

    <input type="hidden" value="{{ $domainsJson }}">

    <div class="row grid-margin">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex align-items-center">
                        <div>
                            <button type="button" class="btn btn-success btn-icon-text font-weight-bold mt-3"
                                onclick="copyDomains();">@lang('general.buttons.CopyToClipboard')</button>
                        </div>

                        <div id="copied_message" class="display-5 mt-3 mb-0 font-weight-medium d-none">
                            <small class="text-success" style="font-size: 15px;">
                                @lang('PagesContent_DomainGenerator.messages.CopiedToClipboard')
                            </small>
                        </div>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center" width="150px">@lang('general.Row')</th>
                                <th>@lang('general.Domain')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($domains as $domain)
                                <tr>
                                    <td class="text-center" width="150px">{{ $loop->index + 1 }}</td>
                                    <td>{{ $domain }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
