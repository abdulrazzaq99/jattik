@php
    $teamContent = getContent('team.content', true);
    $teamElements = getContent('team.element', orderById: true);
@endphp

<section class="team-section py-120">
    <div class="container">
        <div class="section-heading">
            <h3 class="section-heading__title"> {{ __(@$teamContent->data_values->heading) }} </h3>
            <p class="section-heading__desc">
                {{ __(@$teamContent->data_values->sub_heading) }}
            </p>
        </div>
        <div class="row gy-5">
            @foreach ($teamElements as $teamElement)
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="team-card">
                        <div class="team-card__thumb">
                            <img src="{{ frontendImage('team', @$teamElement->data_values->member, '290x345') }}" alt="team member">
                        </div>
                        <div class="team-card__content">
                            <h6 class="team-card__title"> {{ __(@$teamElement->data_values->name) }} </h6>
                            <p class="team-card__designation">
                                {{ __(@$teamElement->data_values->designation) }}
                            </p>
                            <div class="team-card__footer">
                                <p class="work-success">{{ __(@$teamElement->data_values->title) }} :</p>
                                <span class="work-count"><span class="icon">
                                        @php echo @$teamElement->data_values->icon @endphp
                                    </span>
                                    {{ @$teamElement->data_values->digit }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
