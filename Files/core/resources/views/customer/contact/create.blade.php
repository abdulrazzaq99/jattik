@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-envelope"></i> @lang('Contact Us')
                    </h5>
                </div>
                <form action="{{ route('customer.contact.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="las la-info-circle"></i>
                            @lang('We\'re here to help! Send us a message and we\'ll respond as soon as possible.')
                        </div>

                        {{-- Name --}}
                        <div class="form-group">
                            <label>@lang('Your Name') <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ auth()->guard('customer')->user()->fullname ?? old('name') }}" required>
                        </div>

                        {{-- Email --}}
                        <div class="form-group">
                            <label>@lang('Email Address') <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ auth()->guard('customer')->user()->email ?? old('email') }}" required>
                        </div>

                        {{-- Phone --}}
                        <div class="form-group">
                            <label>@lang('Phone Number')</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ auth()->guard('customer')->user()->mobile ?? old('phone') }}">
                        </div>

                        {{-- Subject --}}
                        <div class="form-group">
                            <label>@lang('Subject') <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control"
                                   placeholder="Brief description of your message" required>
                        </div>

                        {{-- Message --}}
                        <div class="form-group">
                            <label>@lang('Message') <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="6"
                                      placeholder="Your message..." required></textarea>
                            <small class="form-text text-muted">@lang('Maximum 2000 characters')</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary">
                            <i class="las la-paper-plane"></i> @lang('Send Message')
                        </button>
                        <a href="{{ route('customer.contact.index') }}" class="btn btn-outline--secondary">
                            <i class="las la-list"></i> @lang('My Messages')
                        </a>
                    </div>
                </form>
            </div>

            {{-- Contact Information --}}
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="las la-phone text--primary" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-3">@lang('Phone')</h6>
                            <p class="text-muted">{{ gs()->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="las la-envelope text--success" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-3">@lang('Email')</h6>
                            <p class="text-muted">{{ gs()->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="lab la-whatsapp text--success" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-3">@lang('WhatsApp')</h6>
                            <p class="text-muted">{{ gs()->whatsapp ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
