@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-section pt-120 pb-120">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                <div class="col-lg-8">
                    <div class="post__details">
                        <div class="post__thumb">
                            <img src="{{ frontendImage('blog', $blog->data_values->blog_image, '700x425') }}" alt="blog image">
                        </div>
                        <div class="post__header">
                            <h4> {{ __($blog->data_values->title) }}</h4>
                        </div>
                        <div class="post__header">
                            @php echo $blog->data_values->description_nic @endphp
                        </div>
                        <div class="row gy-4 justify-content-between">
                            <div class="col-md-6">
                                <h6 class="post__share__title">@lang('Share now')</h6>
                                <ul class="post__share">
                                    <li>
                                        <a target="_blank" class="t-link social-list__icon"
                                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}">
                                            <i class="fab la-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a target="_blank" class="t-link social-list__icon"
                                            href="https://twitter.com/intent/tweet?text={{ __(@$blog->data_values->title) }}%0A{{ url()->current() }}">
                                            <i class="fa-brands fa-x-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a target="_blank" class="t-link social-list__icon"
                                            href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __(@$blog->data_values->title) }}&amp;summary={{ __(@$blog->data_values->description) }}">
                                            <i class="fab la-linkedin-in"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a target="_blank" class="t-link social-list__icon"
                                            href="https://www.instagram.com/share?url={{ urlencode(url()->current()) }}" target="_blank">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="comments-area">
                            <div class="comment-area comments-list">
                                <div class="fb-comments" data-href="{{ url()->current() }}" data-numposts="5"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <aside class="blog-sidebar bg--section">
                        <div class="widget widget__post__area">
                            <h5 class="widget__title">@lang('Recent Post')</h5>
                            <ul>
                                @foreach ($recentBlogs as $recentBlog)
                                    <li>
                                        <a href="{{ route('blog.details', $recentBlog->slug) }}" class="widget__post">
                                            <div class="widget__post__thumb">
                                                <img src="{{ frontendImage('blog', '/thumb_' . @$recentBlog->data_values->blog_image, '415x315') }}"
                                                    alt="blog image">
                                            </div>
                                            <div class="widget__post__content">
                                                <h6 class="widget__post__title">
                                                    {{ __($recentBlog->data_values->title) }}
                                                </h6>
                                                <span>{{ showDateTime($recentBlog->created_at, 'd M Y') }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush
