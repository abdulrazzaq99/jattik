@extends($activeTemplate . 'layouts.app')
@section('panel')
    <section class="maintenance-page flex-column justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-8">
                    <img class="mw-100"
                        src="{{ getImage(getFilePath('maintenance') . '/' . @$maintenance->data_values->image, getFileSize('maintenance')) }}"
                        alt="image">
                </div>
            </div>
            <p class="mx-auto text-center">@php echo $maintenance->data_values->description @endphp</p>

        </div>
    </section>
@endsection

@push('style')
    <style>
        header {
            display: none;
        }

        footer {
            display: none;
        }

        .breadcrumb {
            display: none;
        }

        body {
            background-color: white;
            display: flex;
            align-items: center;
            height: 100vh;
            justify-content: center;
        }
    </style>
@endpush
