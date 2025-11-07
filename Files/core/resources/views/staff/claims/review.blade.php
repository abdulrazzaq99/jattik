@extends('staff.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header bg--warning">
                    <h5 class="card-title text-white mb-0">
                        <i class="las la-clipboard-check"></i> @lang('Review Claim') - {{ $claim->claim_number }}
                    </h5>
                </div>
                <div class="card-body">
                    {{-- SLA Status --}}
                    <div class="alert
                        @if($claim->isOverdue()) alert-danger
                        @elseif($claim->days_until_deadline <= 2) alert-warning
                        @else alert-info
                        @endif text-center mb-4">
                        <h5 class="mb-2">
                            @if($claim->isOverdue())
                                <i class="las la-exclamation-triangle"></i> @lang('OVERDUE - Immediate Action Required')
                            @elseif($claim->days_until_deadline <= 2)
                                <i class="las la-clock"></i> @lang('Urgent - SLA Approaching')
                            @else
                                <i class="las la-info-circle"></i> @lang('Within SLA')
                            @endif
                        </h5>
                        <p class="mb-0">
                            <strong>{{ abs($claim->days_until_deadline) }}</strong>
                            @if($claim->isOverdue())
                                @lang('days overdue')
                            @else
                                @lang('days remaining to resolve')
                            @endif
                        </p>
                    </div>

                    <div class="row">
                        {{-- Left Column - Claim Details --}}
                        <div class="col-md-6">
                            <h6><i class="las la-info-circle"></i> @lang('Claim Information')</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>@lang('Claim #'):</strong></td>
                                    <td>{{ $claim->claim_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Type'):</strong></td>
                                    <td>
                                        <span class="badge badge--danger">
                                            {{ ucwords(str_replace('_', ' ', $claim->claim_type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Submitted'):</strong></td>
                                    <td>{{ showDateTime($claim->submitted_at) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Claimed Amount'):</strong></td>
                                    <td><h5 class="text--primary mb-0">{{ showAmount($claim->claimed_amount) }}</h5></td>
                                </tr>
                            </table>

                            {{-- Customer Information --}}
                            @if($claim->customer)
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6><i class="las la-user"></i> @lang('Customer Details')</h6>
                                        <p class="mb-1"><strong>@lang('Name'):</strong> {{ $claim->customer->fullname }}</p>
                                        <p class="mb-1"><strong>@lang('Email'):</strong> {{ $claim->customer->email }}</p>
                                        <p class="mb-0"><strong>@lang('Mobile'):</strong> {{ $claim->customer->mobile }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Right Column - Shipment Details --}}
                        <div class="col-md-6">
                            @if($claim->courierInfo)
                                <h6><i class="las la-box"></i> @lang('Related Shipment')</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>@lang('Tracking'):</strong></td>
                                        <td>{{ $claim->courierInfo->code }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('Sender'):</strong></td>
                                        <td>{{ $claim->courierInfo->senderCustomer->fullname ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('Receiver'):</strong></td>
                                        <td>{{ $claim->courierInfo->receiverCustomer->fullname ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('Status'):</strong></td>
                                        <td>
                                            @if($claim->courierInfo->status == 3)
                                                <span class="badge badge--success">@lang('Delivered')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('In Transit')</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                                <a href="{{ route('staff.courier.details', $claim->courierInfo->id) }}"
                                   class="btn btn-sm btn--info">
                                    <i class="las la-eye"></i> @lang('View Shipment Details')
                                </a>
                            @endif

                            {{-- Related Issue --}}
                            @if($claim->supportIssue)
                                <div class="alert alert-info mt-3">
                                    <strong><i class="las la-link"></i> @lang('Related Issue'):</strong><br>
                                    {{ $claim->supportIssue->issue_number }} -
                                    {{ ucwords(str_replace('_', ' ', $claim->supportIssue->issue_type)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Claim Description --}}
                    <div class="mb-4">
                        <h6><i class="las la-file-alt"></i> @lang('Claim Description')</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                {{ $claim->description }}
                            </div>
                        </div>
                    </div>

                    {{-- Evidence Gallery --}}
                    @if($claim->evidence && count(json_decode($claim->evidence, true) ?? []) > 0)
                        <div class="mb-4">
                            <h6><i class="las la-images"></i> @lang('Supporting Evidence')</h6>
                            <div class="row">
                                @foreach(json_decode($claim->evidence, true) as $evidence)
                                    <div class="col-md-2 mb-3">
                                        <div class="card">
                                            <div class="card-body text-center p-2">
                                                @if(str_contains($evidence['mime'] ?? '', 'image'))
                                                    <a href="{{ asset('storage/' . $evidence['path']) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $evidence['path']) }}"
                                                             class="img-fluid rounded" style="max-height: 120px;">
                                                    </a>
                                                @else
                                                    <i class="las la-file-pdf text--danger" style="font-size: 3rem;"></i>
                                                @endif
                                                <br>
                                                <a href="{{ asset('storage/' . $evidence['path']) }}" download
                                                   class="btn btn-sm btn-outline--primary mt-2">
                                                    <i class="las la-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <hr class="my-4">

                    {{-- Decision Forms --}}
                    <div class="row">
                        {{-- Approve Form --}}
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg--success">
                                    <h6 class="card-title text-white mb-0">
                                        <i class="las la-check-circle"></i> @lang('Approve Claim')
                                    </h6>
                                </div>
                                <form action="{{ route('staff.claims.approve', $claim->id) }}" method="POST">
                                    @csrf
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>@lang('Approved Amount') <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs()->cur_sym }}</span>
                                                <input type="number" name="approved_amount" class="form-control"
                                                       step="0.01" min="0" max="{{ $claim->claimed_amount }}"
                                                       value="{{ $claim->claimed_amount }}" required>
                                            </div>
                                            <small class="form-text text-muted">
                                                @lang('Maximum'): {{ showAmount($claim->claimed_amount) }}
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <label>@lang('Admin Notes (Optional)')</label>
                                            <textarea name="admin_notes" class="form-control" rows="3"
                                                      placeholder="Internal notes about this decision..."></textarea>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn--success w-100"
                                                onclick="return confirm('@lang("Approve this claim? Customer will be notified.")')">
                                            <i class="las la-check-circle"></i> @lang('Approve Claim')
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Reject Form --}}
                        <div class="col-md-6">
                            <div class="card border-danger">
                                <div class="card-header bg--danger">
                                    <h6 class="card-title text-white mb-0">
                                        <i class="las la-times-circle"></i> @lang('Reject Claim')
                                    </h6>
                                </div>
                                <form action="{{ route('staff.claims.reject', $claim->id) }}" method="POST">
                                    @csrf
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>@lang('Rejection Reason') <span class="text-danger">*</span></label>
                                            <textarea name="rejection_reason" class="form-control" rows="5"
                                                      placeholder="Explain why this claim is being rejected..." required></textarea>
                                            <small class="form-text text-muted">
                                                @lang('This will be sent to the customer')
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <label>@lang('Admin Notes (Optional)')</label>
                                            <textarea name="admin_notes" class="form-control" rows="2"
                                                      placeholder="Internal notes..."></textarea>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn--danger w-100"
                                                onclick="return confirm('@lang("Reject this claim? This action cannot be undone.")')">
                                            <i class="las la-times-circle"></i> @lang('Reject Claim')
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('staff.claims.pending') }}" class="btn btn-sm btn-outline--secondary">
                        <i class="las la-arrow-left"></i> @lang('Back to Pending Claims')
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Auto-fill approved amount with claimed amount
        $('input[name="approved_amount"]').on('input', function() {
            let claimed = parseFloat('{{ $claim->claimed_amount }}');
            let approved = parseFloat($(this).val());

            if (approved > claimed) {
                alert('@lang("Approved amount cannot exceed claimed amount")');
                $(this).val(claimed);
            }
        });
    });
</script>
@endpush
