@php
    $branchContent = getContent('branch.content', true);
    $branchElements = getContent('branch.element', orderById: true);
@endphp

<section class="branches-section py-120 section-bg">
    <div class="branches-animation">
        <span class="line"></span>
        <span class="line"></span>
        <span class="line"></span>
    </div>
    <div class="container">
        <div class="section-heading">
            <h3 class="section-heading__title">{{ __(@$branchContent->data_values->heading) }}</h3>
            <p class="section-heading__desc">{{ __(@$branchContent->data_values->sub_heading) }}</p>
        </div>
        <div class="row gy-3 gx-5">
            @foreach (@$branchElements as $branchElement)
                <div class="col-lg-3 col-sm-6 col-xsm-6">
                    <div class="branch-card">
                        <h6 class="branch-card__title">{{ __(@$branchElement->data_values->branch_name) }}</h6>
                        <div class="branch-card__content">
                            <ul class="branch-card__list">
                                <li class="branch-card__item">
                                    <span class="icon"><i class="icon-Location-Icon"></i></span>
                                    <div class="content">
                                        <p class="title">@lang('Location')</p>
                                        <span class="desc d-block">
                                            {{ __(@$branchElement->data_values->location) }}
                                        </span>
                                    </div>
                                </li>
                                <li class="branch-card__item">
                                    <span class="icon"><i class="icon-phone-call"></i></span>
                                    <div class="content">
                                        <p class="title">@lang('Mobile')</p>
                                        <a class="desc d-block" href="tel:{{ @$branchElement->data_values->mobile }}">
                                            {{ @$branchElement->data_values->mobile }}
                                        </a>
                                    </div>
                                </li>
                                <li class="branch-card__item">
                                    <span class="icon"><i class="icon-email"></i></span>
                                    <div class="content">
                                        <p class="title">@lang('Email')</p>
                                        <a class="desc d-block" href="mailto:{{ @$branchElement->data_values->email }}">
                                            {{ @$branchElement->data_values->email }}
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
