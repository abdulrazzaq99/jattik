@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-120">
        <div class="container">
            @php
                echo @$cookie->data_values->description;
            @endphp
    </section>
@endsection
