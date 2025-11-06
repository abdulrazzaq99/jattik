@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-section pt-120 pb-120">
        <div class="container">
            <div class="row g-4 justify-content-center">
                @foreach ($blogs as $blog)
                    <div class="col-lg-4 col-md-6 col-sm-10">
                        <div class="post__item">
                            <div class="post__thumb">
                                <a href="{{ route('blog.details', $blog->slug) }}">
                                    <img src="{{ frontendImage('blog', 'thumb_' . @$blog->data_values->blog_image, '415x315') }}" alt="blog image">
                                </a>
                                <div class="post__date">
                                    <h4 class="date">{{ showDateTime($blog->created_at, 'd') }}</h4>
                                    <span>{{ showDateTime($blog->created_at, 'M') }}</span>
                                </div>
                            </div>
                            <div class="post__content bg--section">
                                <h5 class="post__title">
                                    <a href="{{ route('blog.details', $blog->slug) }}">
                                        {{ __($blog->data_values->title) }}
                                    </a>
                                </h5>
                                <a href="{{ route('blog.details', $blog->slug) }}">
                                    @lang('Read More')
                                    <i class="las la-long-arrow-alt-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if ($blogs->hasPages())
                    {{ paginateLinks($blogs) }}
                @endif
            </div>
        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
