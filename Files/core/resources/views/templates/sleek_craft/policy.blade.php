@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-120">
        <div class="container">
            @php
                echo @$policy->data_values->details;
            @endphp
        </div>
    </section>
@endsection
