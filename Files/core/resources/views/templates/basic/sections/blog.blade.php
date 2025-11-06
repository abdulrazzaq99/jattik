@php
    $blogContent = getContent('blog.content', true);
    $blogElements = getContent('blog.element', false, 3);
@endphp

<section class="blog-section pt-120 pb-120">
    <div class="container">
        <div class="section__header section__header__center">
            <span class="section__cate">
                {{ __(@$blogContent->data_values->title) }}
            </span>
            <h3 class="section__title">{{ __(@$blogContent->data_values->heading) }}</h3>
            <p>{{ __(@$blogContent->data_values->sub_heading) }}</p>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ($blogElements as $blogElement)
                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="post__item">
                        <div class="post__thumb">
                            <a href="{{ route('blog.details', $blogElement->slug) }}">
                                <img src="{{ frontendImage('blog', 'thumb_' . @$blogElement->data_values->blog_image, '415x315') }}"
                                    alt="blog image">
                            </a>
                            <div class="post__date">
                                <h4 class="date">{{ showDateTime($blogElement->created_at, 'd') }}</h4>
                                <span>{{ showDateTime($blogElement->created_at, 'M') }}</span>
                            </div>
                        </div>
                        <div class="post__content bg--section">
                            <h5 class="post__title">
                                <a href="{{ route('blog.details', $blogElement->slug) }}">
                                    {{ __($blogElement->data_values->title) }}
                                </a>
                            </h5>
                            <a href="{{ route('blog.details', $blogElement->slug) }}">
                                @lang('Read More')
                                <i class="las la-long-arrow-alt-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
