@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-detials py-120">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                <div class="col-xl-9 col-lg-8">
                    <div class="blog-details">
                        <div class="blog-details__thumb">
                            <img class="fit-image" src="{{ frontendImage('blog', @$blog->data_values->blog_image, '965x450') }}" alt="blog">
                        </div>
                        <div class="blog-details__content">
                            <span class="blog-item__date mb-2">
                                <span class="blog-item__date-icon">
                                    <i class="las la-clock"></i>
                                </span>
                                {{ diffForHumans(@$blog->created_at) }}
                            </span>
                            <h3 class="blog-details__title"> {{ __(@$blog->data_values->title) }} </h3>
                            <div>
                                @php echo @$blog->data_values->description_nic; @endphp
                            </div>
                            <div class="blog-details__share d-flex align-items-center mt-4 flex-wrap">
                                <h5 class="social-share__title me-sm-3 d-inline-block mb-0 me-1">@lang('Share:')</h5>
                                <ul class="social-list">
                                    <li class="social-list__item">
                                        <a class="social-list__link flex-center"
                                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a class="social-list__link flex-center"
                                            href="https://twitter.com/intent/tweet?text=my share text&amp;url={{ urlencode(url()->current()) }}"
                                            target="_blank">
                                            <i class="fa-brands fa-x-twitter"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a class="social-list__link flex-center"
                                            href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title=my share text&amp;summary=dit is de linkedin summary"
                                            target="_blank">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a class="social-list__link flex-center"
                                            href="https://www.instagram.com/share?url={{ urlencode(url()->current()) }}" target="_blank">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <div class="blog-sidebar-wrapper">
                        <div class="blog-sidebar">
                            <h5 class="blog-sidebar__title"> @lang('Latest Blogs') </h5>
                            @foreach (@$recentBlogs as $blog)
                                <div class="latest-blog">
                                    <div class="latest-blog__thumb">
                                        <a href="{{ route('blog.details', @$blog->slug) }}">
                                            <img class="fit-image"
                                                src="{{ frontendImage('blog', 'thumb_' . @$blog->data_values->blog_image, '385x190') }}" alt="blog">
                                        </a>
                                    </div>
                                    <div class="latest-blog__content">
                                        <h6 class="latest-blog__title">
                                            <a href="{{ route('blog.details', @$blog->slug) }}">
                                                {{ __(strLimit(@$blog->data_values->title, 70)) }}
                                            </a>
                                        </h6>
                                        <span class="latest-blog__date fs-13">
                                            {{ showDateTime($blog->created_at, 'd F Y') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @push('fbComment')
        @php echo loadExtension('fb-comment') @endphp
    @endpush
@endsection
