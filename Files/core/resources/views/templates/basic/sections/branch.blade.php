@php
    $branchContent = getContent('branch.content', true);
    $branchesList = App\Models\Branch::active()->orderBy('name')->get();
@endphp

<div class="contact-brances position-relative pt-120 pb-120 bg--title-overlay bg_img bg_fixed"
    data-background="{{ frontendImage('branch', @$branchContent->data_values->background_image, '1920x1080') }}">
    <div class="container position-relative">
        <div class="row gy-5 align-items-center">
            <div class="col-lg-12">
                <div class="brances-slider-wrapper ps-xl-4">
                    <div class="section__header section__header__center text--white">
                        <span class="section__cate">{{ __(@$branchContent->data_values->title) }}</span>
                        <h3 class="section__title">{{ __(@$branchContent->data_values->heading) }}</h3>
                        <p>{{ __(@$branchContent->data_values->sub_heading) }}</p>
                    </div>
                    <div class="brances-slider owl-theme owl-carousel">
                        @foreach ($branchesList as $branch)
                            <div class="brance__item">
                                <h6 class="title">{{ __($branch->name) }}</h6>
                                <ul class="footer__widget-contact">
                                    <li>
                                        <i class="las la-map-marker"></i> {{ __($branch->address) }}
                                    </li>
                                    <li>
                                        <a href="Tel:{{ $branch->phone }}">
                                            <i class="las la-mobile"></i> @lang('Mobile'): {{ $branch->phone }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="Mailto:{{ @$branch->email }}">
                                            <i class="las la-envelope"></i> {{ $branch->email }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
