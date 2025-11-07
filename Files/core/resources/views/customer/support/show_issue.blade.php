@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-ticket-alt"></i> @lang('Issue Details') - {{ $issue->issue_number }}
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Status and Priority Badges --}}
                    <div class="mb-4">
                        @if($issue->status == 0)
                            <span class="badge badge--warning badge-lg">
                                <i class="las la-exclamation-circle"></i> @lang('Open')
                            </span>
                        @elseif($issue->status == 1)
                            <span class="badge badge--info badge-lg">
                                <i class="las la-spinner"></i> @lang('In Progress')
                            </span>
                        @elseif($issue->status == 2)
                            <span class="badge badge--success badge-lg">
                                <i class="las la-check-circle"></i> @lang('Resolved')
                            </span>
                        @else
                            <span class="badge badge--dark badge-lg">
                                <i class="las la-times-circle"></i> @lang('Closed')
                            </span>
                        @endif

                        @if($issue->priority == 3)
                            <span class="badge badge--danger badge-lg ms-2">
                                <i class="las la-exclamation-triangle"></i> @lang('High Priority')
                            </span>
                        @elseif($issue->priority == 2)
                            <span class="badge badge--warning badge-lg ms-2">@lang('Medium Priority')</span>
                        @else
                            <span class="badge badge--info badge-lg ms-2">@lang('Low Priority')</span>
                        @endif

                        {{-- Issue Type --}}
                        <span class="badge badge--primary badge-lg ms-2">
                            @if($issue->issue_type == 'wrong_parcel')
                                @lang('Wrong Parcel')
                            @elseif($issue->issue_type == 'damaged')
                                @lang('Damaged')
                            @elseif($issue->issue_type == 'missing')
                                @lang('Missing')
                            @elseif($issue->issue_type == 'delay')
                                @lang('Delay')
                            @else
                                @lang('Other')
                            @endif
                        </span>
                    </div>

                    {{-- Shipment Information --}}
                    @if($issue->courierInfo)
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="las la-box"></i> @lang('Related Shipment')</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <strong>@lang('Tracking Code'):</strong><br>
                                            <span class="badge badge--dark">{{ $issue->courierInfo->code }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <strong>@lang('Current Status'):</strong><br>
                                            @if($issue->courierInfo->status == 3)
                                                <span class="badge badge--success">@lang('Delivered')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('In Transit')</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('customer.track', ['code' => $issue->courierInfo->code]) }}"
                                   class="btn btn-sm btn--primary mt-2">
                                    <i class="las la-search-location"></i> @lang('Track Shipment')
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Issue Description --}}
                    <div class="mb-4">
                        <h6><i class="las la-file-alt"></i> @lang('Issue Description')</h6>
                        <div class="alert alert-info">
                            {{ $issue->description }}
                        </div>
                    </div>

                    {{-- Attachments --}}
                    @if($issue->attachments && count(json_decode($issue->attachments, true) ?? []) > 0)
                        <div class="mb-4">
                            <h6><i class="las la-paperclip"></i> @lang('Attachments')</h6>
                            <div class="row">
                                @foreach(json_decode($issue->attachments, true) as $attachment)
                                    <div class="col-md-3 mb-2">
                                        <div class="card">
                                            <div class="card-body text-center p-2">
                                                @if(str_contains($attachment['mime'] ?? '', 'image'))
                                                    <img src="{{ asset('storage/' . $attachment['path']) }}"
                                                         class="img-fluid rounded" style="max-height: 100px;">
                                                @else
                                                    <i class="las la-file-pdf text--danger" style="font-size: 3rem;"></i>
                                                @endif
                                                <br>
                                                <a href="{{ asset('storage/' . $attachment['path']) }}"
                                                   class="btn btn-sm btn-outline--primary mt-2" download>
                                                    <i class="las la-download"></i> @lang('Download')
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Timeline --}}
                    <div class="mb-4">
                        <h6><i class="las la-history"></i> @lang('Issue Timeline')</h6>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <i class="las la-plus-circle text--success"></i>
                                <strong>@lang('Created')</strong> - {{ showDateTime($issue->submitted_at) }}
                                <span class="text-muted small">({{ diffForHumans($issue->submitted_at) }})</span>
                            </li>
                            @if($issue->assigned_at)
                                <li class="list-group-item">
                                    <i class="las la-user-check text--info"></i>
                                    <strong>@lang('Assigned to Staff')</strong> - {{ showDateTime($issue->assigned_at) }}
                                    @if($issue->assignedTo)
                                        <br><small class="text-muted">Assigned to: {{ $issue->assignedTo->fullname }}</small>
                                    @endif
                                </li>
                            @endif
                            @if($issue->resolved_at)
                                <li class="list-group-item">
                                    <i class="las la-check-circle text--success"></i>
                                    <strong>@lang('Resolved')</strong> - {{ showDateTime($issue->resolved_at) }}
                                </li>
                            @endif
                            @if($issue->closed_at)
                                <li class="list-group-item">
                                    <i class="las la-times-circle text--dark"></i>
                                    <strong>@lang('Closed')</strong> - {{ showDateTime($issue->closed_at) }}
                                </li>
                            @endif
                        </ul>
                    </div>

                    {{-- Resolution (if resolved) --}}
                    @if($issue->resolution)
                        <div class="card border-success">
                            <div class="card-header bg--success">
                                <h6 class="card-title text-white mb-0">
                                    <i class="las la-check-circle"></i> @lang('Resolution')
                                </h6>
                            </div>
                            <div class="card-body">
                                {{ $issue->resolution }}
                                @if($issue->resolved_at)
                                    <p class="mb-0 mt-3 text-muted small">
                                        @lang('Resolved on') {{ showDateTime($issue->resolved_at) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Expected Resolution --}}
                    @if($issue->expected_resolution)
                        <div class="mt-3">
                            <p class="text-muted small">
                                <strong>@lang('Expected Resolution:')}</strong> {{ $issue->expected_resolution }}
                            </p>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('customer.support.issues') }}" class="btn btn-sm btn-outline--secondary">
                        <i class="las la-arrow-left"></i> @lang('Back to Issues')
                    </a>
                    @if(in_array($issue->status, [0, 1]) && !$issue->related_claim_id)
                        <a href="{{ route('customer.support.claims.create', ['issue_id' => $issue->id]) }}"
                           class="btn btn-sm btn--warning float-end">
                            <i class="las la-file-invoice-dollar"></i> @lang('File Claim for This Issue')
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
