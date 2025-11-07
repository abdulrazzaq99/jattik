@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('My Shipping Quotes')</h5>
                    <a href="{{ route('customer.shipment.calculator') }}" class="btn btn-sm btn--primary">
                        <i class="las la-plus"></i> @lang('New Quote')
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($quotes->count() > 0)
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Quote Number')</th>
                                        <th>@lang('Courier')</th>
                                        <th>@lang('Route')</th>
                                        <th>@lang('Weight')</th>
                                        <th>@lang('Total Fee')</th>
                                        <th>@lang('Valid Until')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Created')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotes as $quote)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">{{ $quote->quote_number }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $quote->courier_name }}</span>
                                                @if($quote->quote_type == 2)
                                                    <br><span class="badge badge--info">@lang('Staff Quote')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>
                                                    @if($quote->originAddress)
                                                        <strong>@lang('From'):</strong> {{ $quote->originAddress->city }}, {{ $quote->originAddress->country }}<br>
                                                    @endif
                                                    @if($quote->destinationAddress)
                                                        <strong>@lang('To'):</strong> {{ $quote->destinationAddress->city }}, {{ $quote->destinationAddress->country }}
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                {{ number_format($quote->total_weight, 2) }} kg
                                                @if($quote->package_count > 1)
                                                    <br><small class="text-muted">({{ $quote->package_count }} @lang('packages'))</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">${{ number_format($quote->total_fee, 2) }}</span>
                                                @if($quote->discount_amount > 0)
                                                    <br><small class="text-success">
                                                        <i class="las la-tag"></i> ${{ number_format($quote->discount_amount, 2) }} @lang('discount')
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($quote->valid_until)
                                                    {{ $quote->valid_until->format('M d, Y') }}
                                                    @if($quote->valid_until->isPast())
                                                        <br><span class="badge badge--danger">@lang('Expired')</span>
                                                    @elseif($quote->valid_until->diffInDays(now()) <= 2)
                                                        <br><span class="badge badge--warning">@lang('Expiring Soon')</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">@lang('N/A')</span>
                                                @endif
                                            </td>
                                            <td>{!! $quote->status_badge !!}</td>
                                            <td>{{ $quote->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @if($quote->notes)
                                            <tr class="bg-light">
                                                <td colspan="8" class="py-2">
                                                    <small><strong>@lang('Notes'):</strong> {{ $quote->notes }}</small>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            {{ $quotes->links() }}
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="las la-file-invoice-dollar" style="font-size: 64px; color: #ccc;"></i>
                            <p class="text-muted mt-3">@lang('No shipping quotes found.')</p>
                            <a href="{{ route('customer.shipment.calculator') }}" class="btn btn--primary mt-2">
                                <i class="las la-calculator"></i> @lang('Calculate Shipping Rates')
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quote Status Legend --}}
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="mb-3"><i class="las la-info-circle"></i> @lang('Quote Status')</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <span class="badge badge--secondary">@lang('Draft')</span> - @lang('Quote saved but not finalized')
                        </div>
                        <div class="col-md-3">
                            <span class="badge badge--info">@lang('Sent')</span> - @lang('Quote sent by staff')
                        </div>
                        <div class="col-md-3">
                            <span class="badge badge--success">@lang('Accepted')</span> - @lang('Quote accepted and ready')
                        </div>
                        <div class="col-md-3">
                            <span class="badge badge--danger">@lang('Expired')</span> - @lang('Quote validity expired')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
