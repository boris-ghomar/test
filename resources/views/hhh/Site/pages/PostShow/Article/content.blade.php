<div class="row grid-margin">
    <div class="col-12">
        <div class="card ql-container post">
            <div>
                <h1 class="title">{!! $post->title !!}</h1>
            </div>
            <div class="card-body ql-editor">
                {!! $htmlContent !!}
            </div>
            @include('hhh.Site.partials.actions._post_actions')
        </div>
    </div>
</div>
@include('hhh.Site.partials._comments')
