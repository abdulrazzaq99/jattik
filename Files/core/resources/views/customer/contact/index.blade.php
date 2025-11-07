@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('My Contact Messages')</h5>
                    <a href="{{ route('customer.contact.create') }}" class="btn btn-sm btn--primary">
                        <i class="las la-plus"></i> @lang('New Message')
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Message')</th>
                                    <th>@lang('Submitted')</th>
                                    <th>@lang('Replied At')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $message)
                                    <tr>
                                        <td>
                                            @if($message->status == 0)
                                                <span class="badge badge--warning">@lang('New')</span>
                                            @elseif($message->status == 1)
                                                <span class="badge badge--info">@lang('Read')</span>
                                            @elseif($message->status == 2)
                                                <span class="badge badge--success">@lang('Replied')</span>
                                            @else
                                                <span class="badge badge--dark">@lang('Closed')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $message->subject }}</strong>
                                            @if($message->status == 2)
                                                <br><small class="text--success">
                                                    <i class="las la-reply"></i> @lang('Admin replied')
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                {{ Str::limit($message->message, 100) }}
                                            </div>
                                            @if($message->admin_reply)
                                                <div class="mt-2 p-2 bg-light rounded">
                                                    <small class="text-muted">
                                                        <strong>@lang('Admin Reply:')}</strong><br>
                                                        {{ Str::limit($message->admin_reply, 80) }}
                                                    </small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($message->created_at) }}<br>
                                            <small class="text-muted">{{ diffForHumans($message->created_at) }}</small>
                                        </td>
                                        <td>
                                            @if($message->replied_at)
                                                {{ showDateTime($message->replied_at) }}<br>
                                                <small class="text-muted">{{ diffForHumans($message->replied_at) }}</small>
                                            @else
                                                <span class="text-muted">@lang('No reply yet')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            <i class="las la-inbox"></i> @lang('No messages found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($messages->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($messages) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-envelope text--primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $totalMessages ?? 0 }}</h3>
                    <p class="text-muted mb-0">@lang('Total Messages')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-clock text--warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $pendingMessages ?? 0 }}</h3>
                    <p class="text-muted mb-0">@lang('Pending')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-reply text--success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $repliedMessages ?? 0 }}</h3>
                    <p class="text-muted mb-0">@lang('Replied')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-check-circle text--info" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $closedMessages ?? 0 }}</h3>
                    <p class="text-muted mb-0">@lang('Closed')</p>
                </div>
            </div>
        </div>
    </div>
@endsection
