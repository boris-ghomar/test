<div class="row grid-margin">
    <div class="col-12">
        <div class="card post post-faq">
            <div>
                <h1 class="title">{!! $post->title !!}</h1>
            </div>
            <div class="card-body">
                {!! $post->content !!}
            </div>
            @include('hhh.Site.partials.actions._post_actions')
        </div>
    </div>
</div>
@include('hhh.Site.partials._comments')
