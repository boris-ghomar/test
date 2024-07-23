<div class="d-flex justify-content-between ">

    <button type="submit" class="btn btn-gradient-primary mr-2">
        @if ($isLastStep)
            @lang('general.buttons.submit')
        @else
            @lang('general.buttons.NextStep')
        @endif
    </button>

    @if (!$isFristStep)
        <a type="button" href="{{ SitePublicRoutesEnum::RegisterBetconstructGoBack->url() }}"
            class="btn btn-secondary mr-2">@lang('general.buttons.PreviousStep')</a>
    @endif
</div>
</form>
