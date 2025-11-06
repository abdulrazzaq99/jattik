@php
    $teamContent = getContent('team.content', true);
    $teamElements = getContent('team.element', null, false, true);
@endphp
<section class="team-section pt-60 pb-120">
    <div class="container">
        <div class="section__header section__header__center">
            <span class="section__cate">{{ __(@$teamContent->data_values->title) }}</span>
            <h3 class="section__title">{{ __(@$teamContent->data_values->heading) }}</h3>
            <p>{{ __(@$teamContent->data_values->sub_heading) }}</p>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ($teamElements as $teamElement)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="team__item">
                        <div class="team__item-thumb">
                            <img src="{{ frontendImage('team', $teamElement->data_values->member, '600x600') }}"
                                alt="team image">
                            <a href="{{ frontendImage('team', $teamElement->data_values->member, '600x600') }}"
                                class="view-img" data-lightbox>
                                <i class="las la-plus"></i>
                            </a>
                        </div>
                        <div class="team__item-content">
                            <h5 class="team__item-title">{{ __($teamElement->data_values->name) }}</h5>
                            <span class="text--base">{{ __($teamElement->data_values->designation) }}</span>
                            <span class="d-block">
                                @lang('Complete Delivery') :
                                <span class="text--base">
                                    {{ __($teamElement->data_values->total_delivery) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
