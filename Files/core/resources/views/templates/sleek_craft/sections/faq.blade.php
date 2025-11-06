@php
    $faqContent = getContent('faq.content', true);
    $faqElements = getContent('faq.element', orderById: true);
@endphp

<section class="faq py-120 section-bg">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-6">
                <div class="section-heading style-left">
                    <h3 class="section-heading__title">
                        {{ __(@$faqContent->data_values->heading) }}
                    </h3>
                    <p class="section-heading__desc">
                        {{ __(@$faqContent->data_values->sub_heading) }}
                    </p>
                </div>
                <a class="btn btn--base" href="{{ @$faqContent->data_values->button_link }}">
                    <span class="btn--icon"><i class="icon-View-More"></i></span>
                    {{ __(@$faqContent->data_values->button_text) }}
                </a>
            </div>
            <div class="col-md-6">
                <div class="accordion custom--accordion" id="faqList">
                    @foreach (@$faqElements as $key => $faqElement)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq{{ $key }}"
                                    type="button" aria-expanded="{{ $key == 0 ? 'true' : 'false' }}" aria-controls="faq{{ $key }}">
                                    {{ __(@$faqElement->data_values->question) }}
                                </button>
                            </h2>
                            <div class="accordion-collapse {{ $key == 0 ? 'show' : '' }} collapse" id="faq{{ $key }}"
                                data-bs-parent="#faqList">
                                <div class="accordion-body">
                                    <p class="text">
                                        {{ __(@$faqElement->data_values->answer) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
