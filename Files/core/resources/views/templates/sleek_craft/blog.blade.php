@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog py-120">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                @foreach ($blogs as $blogElement)
                    <div class="col-lg-4 col-md-6">
                        <div class="blog-item section-bg">
                            <div class="blog-item__thumb">
                                <a class="blog-item__thumb-link" href="{{ route('blog.details', @$blogElement->slug) }}">
                                    <img class="fit-image"
                                        src="{{ frontendImage('blog', 'thumb_' . @$blogElement->data_values->blog_image, '385x190') }}"
                                        alt="blog">
                                </a>
                            </div>
                            <div class="blog-item__content">
                                <ul class="text-list flex-between gap-3">
                                    <li class="text-list__item fs-14">
                                        <span class="text-list__item-icon text--base me-1">
                                            <i class="las la-clock"></i>
                                        </span>
                                        {{ diffForHumans(@$blogElement->created_at) }}
                                    </li>
                                </ul>
                                <h6 class="blog-item__title">
                                    <a class="blog-item__title-link border-effect"
                                        href="{{ route('blog.details', @$blogElement->slug) }}">
                                        {{ __(strLimit(@$blogElement->data_values->title, 80)) }}
                                    </a>
                                </h6>
                                <div class="blog-item__btn">
                                    <a href="{{ route('blog.details', @$blogElement->slug) }}" class="blog-item__btn-link">
                                        @lang('Read More')
                                        <span class="blog-item__icon">
                                            <i class="las la-angle-right"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <nav class="pt-60" aria-label="Page navigation example">
            @if ($blogs->hasPages())
                {{ paginateLinks($blogs) }}
            @endif
        </nav>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
