@php
    $faqContent = getContent('faq.content', true);
    $faqElements = getContent('faq.element', null, false, true);
@endphp
<section class="faqs-section pt-120 pb-120">
    <div class="container">
        <div class="row justify-content-between gy-5 align-items-end">
            <div class="col-lg-6">
                <div class="section__header">
                    <span class="section__cate">{{ __(@$faqContent->data_values->title) }}</span>
                    <h3 class="section__title">{{ __(@$faqContent->data_values->heading) }}</h3>
                    <p>
                        {{ __(@$faqContent->data_values->sub_heading) }}
                    </p>
                </div>
                <div class="faq__wrapper">
                    @foreach ($faqElements as $faqElement)
                        <div class="faq__item">
                            <div class="faq__title">
                                <h5 class="title">{{ __($faqElement->data_values->question) }}</h5>
                                <span class="right-icon"></span>
                            </div>
                            <div class="faq__content">
                                <p>{{ __($faqElement->data_values->answer) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="faqs-thumb">
                    <img src="{{ frontendImage('faq', @$faqContent->data_values->faq_image, '651x464') }}"
                        alt="faqs image">
                </div>
            </div>
        </div>
    </div>
</section>
