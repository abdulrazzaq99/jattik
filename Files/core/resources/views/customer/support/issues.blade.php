@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('My Issues')</h5>
                    <a href="{{ route('customer.support.issues.create') }}" class="btn btn-sm btn--primary">
                        <i class="las la-plus"></i> @lang('Report New Issue')
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Issue #')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Courier')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Submitted')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($issues as $issue)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text--primary">{{ $issue->issue_number }}</span>
                                        </td>
                                        <td>
                                            @if($issue->issue_type == 'wrong_parcel')
                                                <span class="badge badge--warning">@lang('Wrong Parcel')</span>
                                            @elseif($issue->issue_type == 'damaged')
                                                <span class="badge badge--danger">@lang('Damaged')</span>
                                            @elseif($issue->issue_type == 'missing')
                                                <span class="badge badge--danger">@lang('Missing')</span>
                                            @elseif($issue->issue_type == 'delay')
                                                <span class="badge badge--warning">@lang('Delay')</span>
                                            @else
                                                <span class="badge badge--info">@lang('Other')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($issue->courierInfo)
                                                <span class="fw-bold">{{ $issue->courierInfo->code }}</span>
                                            @else
                                                <span class="text-muted">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($issue->priority == 1)
                                                <span class="badge badge--info">@lang('Low')</span>
                                            @elseif($issue->priority == 2)
                                                <span class="badge badge--warning">@lang('Medium')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('High')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($issue->status == 0)
                                                <span class="badge badge--warning">@lang('Open')</span>
                                            @elseif($issue->status == 1)
                                                <span class="badge badge--info">@lang('In Progress')</span>
                                            @elseif($issue->status == 2)
                                                <span class="badge badge--success">@lang('Resolved')</span>
                                            @else
                                                <span class="badge badge--dark">@lang('Closed')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($issue->submitted_at) }}<br>
                                            <span class="small text-muted">{{ diffForHumans($issue->submitted_at) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('customer.support.issues.show', $issue->id) }}"
                                               class="btn btn-sm btn-outline--primary">
                                                <i class="la la-eye"></i> @lang('View')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            <i class="las la-info-circle"></i> @lang('No issues reported yet')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($issues->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($issues) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row mt-4">
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-exclamation-circle text--primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $openCount }}</h3>
                    <p class="text-muted mb-0">@lang('Open Issues')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-spinner text--warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $inProgressCount }}</h3>
                    <p class="text-muted mb-0">@lang('In Progress')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-check-circle text--success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $resolvedCount }}</h3>
                    <p class="text-muted mb-0">@lang('Resolved')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-list text--info" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $totalIssues }}</h3>
                    <p class="text-muted mb-0">@lang('Total Issues')</p>
                </div>
            </div>
        </div>
    </div>
@endsection
