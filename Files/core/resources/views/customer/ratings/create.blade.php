@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-star"></i> @lang('Rate Your Experience')
                    </h5>
                </div>
                <form action="{{ route('customer.ratings.store', $courier->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        {{-- Shipment Details --}}
                        <div class="alert alert-info">
                            <h6 class="mb-2"><i class="las la-box"></i> @lang('Shipment Details')</h6>
                            <p class="mb-1"><strong>@lang('Tracking Code'):</strong> {{ $courier->code }}</p>
                            <p class="mb-1"><strong>@lang('Receiver'):</strong> {{ $courier->receiverCustomer->fullname ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>@lang('Delivered'):</strong> {{ showDateTime($courier->delivery_date) }}</p>
                        </div>

                        {{-- Overall Rating --}}
                        <div class="form-group">
                            <label>@lang('Overall Rating') <span class="text-danger">*</span></label>
                            <div class="star-rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="overall-{{ $i }}" name="overall_rating" value="{{ $i }}" required>
                                    <label for="overall-{{ $i }}" class="star">&#9733;</label>
                                @endfor
                            </div>
                            <small class="form-text text-muted">@lang('Rate your overall experience')</small>
                        </div>

                        {{-- Speed Rating --}}
                        <div class="form-group">
                            <label>@lang('Delivery Speed')</label>
                            <div class="star-rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="speed-{{ $i }}" name="speed_rating" value="{{ $i }}">
                                    <label for="speed-{{ $i }}" class="star">&#9733;</label>
                                @endfor
                            </div>
                            <small class="form-text text-muted">@lang('How fast was the delivery?')</small>
                        </div>

                        {{-- Packaging Rating --}}
                        <div class="form-group">
                            <label>@lang('Packaging Quality')</label>
                            <div class="star-rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="packaging-{{ $i }}" name="packaging_rating" value="{{ $i }}">
                                    <label for="packaging-{{ $i }}" class="star">&#9733;</label>
                                @endfor
                            </div>
                            <small class="form-text text-muted">@lang('How was the packaging condition?')</small>
                        </div>

                        {{-- Service Rating --}}
                        <div class="form-group">
                            <label>@lang('Customer Service')</label>
                            <div class="star-rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="service-{{ $i }}" name="service_rating" value="{{ $i }}">
                                    <label for="service-{{ $i }}" class="star">&#9733;</label>
                                @endfor
                            </div>
                            <small class="form-text text-muted">@lang('Rate our customer service')</small>
                        </div>

                        {{-- Comment --}}
                        <div class="form-group">
                            <label>@lang('Your Feedback')</label>
                            <textarea name="comment" class="form-control" rows="4"
                                      placeholder="Share your experience with us..."></textarea>
                            <small class="form-text text-muted">@lang('Optional, but helps us improve')</small>
                        </div>

                        {{-- Recommendation --}}
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="recommend" name="would_recommend" value="1" checked>
                                <label class="custom-control-label" for="recommend">
                                    @lang('I would recommend CourierLab to others')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary">
                            <i class="las la-paper-plane"></i> @lang('Submit Rating')
                        </button>
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-outline--secondary">
                            <i class="las la-times"></i> @lang('Cancel')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .star-rating {
        direction: rtl;
        display: inline-flex;
        font-size: 2.5rem;
    }
    .star-rating input[type="radio"] {
        display: none;
    }
    .star-rating label.star {
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-rating input[type="radio"]:checked ~ label.star,
    .star-rating label.star:hover,
    .star-rating label.star:hover ~ label.star {
        color: #ffc107;
    }
</style>
@endpush
