@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="lab la-whatsapp"></i> @lang('WhatsApp Conversations')
                    </h5>
                    <div>
                        <a href="{{ route('admin.whatsapp.escalated') }}" class="btn btn-sm btn--warning">
                            <i class="las la-exclamation-triangle"></i> @lang('Escalated')
                        </a>
                        <a href="{{ route('admin.whatsapp.settings') }}" class="btn btn-sm btn--primary">
                            <i class="las la-cog"></i> @lang('Settings')
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Phone Number')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Last Message')</th>
                                    <th>@lang('Total Messages')</th>
                                    <th>@lang('Escalated')</th>
                                    <th>@lang('Last Contact')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($conversations as $conversation)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $conversation->phone_number }}</span>
                                        </td>
                                        <td>
                                            @if($conversation->messages->first()?->customer)
                                                <span>{{ $conversation->messages->first()->customer->fullname }}</span><br>
                                                <span class="small text-muted">{{ $conversation->messages->first()->customer->email }}</span>
                                            @else
                                                <span class="text-muted">@lang('Guest')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $lastMsg = $conversation->messages->first();
                                            @endphp
                                            @if($lastMsg)
                                                <div class="d-flex align-items-start">
                                                    @if($lastMsg->direction == 'outbound')
                                                        <i class="las la-arrow-right text--primary me-2"></i>
                                                    @else
                                                        <i class="las la-arrow-left text--info me-2"></i>
                                                    @endif
                                                    <small>{{ Str::limit($lastMsg->message_content, 50) }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ $conversation->message_count }}</span>
                                        </td>
                                        <td>
                                            @if($conversation->messages->where('escalated_to_human', true)->count() > 0)
                                                <span class="badge badge--warning">
                                                    <i class="las la-exclamation-circle"></i> @lang('Yes')
                                                </span>
                                            @else
                                                <span class="badge badge--success">@lang('No')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($conversation->last_message_at) }}<br>
                                            <span class="small text-muted">{{ diffForHumans($conversation->last_message_at) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.whatsapp.conversation', $conversation->phone_number) }}"
                                               class="btn btn-sm btn-outline--primary">
                                                <i class="las la-comments"></i> @lang('View')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            <i class="lab la-whatsapp"></i> @lang('No conversations found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($conversations->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($conversations) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="lab la-whatsapp text--success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $totalConversations ?? 0 }}</h3>
                    <p class="text-muted mb-0">@lang('Total Conversations')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-calendar-day text--primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $todayMessages ?? 0 }}</h3>
                    <p class="text-muted mb-0">@lang('Messages Today')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-exclamation-triangle text--warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $escalatedCount ?? 0 }}</h3>
                    <p class="text-muted mb-0">@lang('Escalated')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-robot text--info" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $botResponseRate ?? 0 }}%</h3>
                    <p class="text-muted mb-0">@lang('Bot Response Rate')</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bot Activity Chart --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-chart-line"></i> @lang('WhatsApp Bot Activity (Last 7 Days)')
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="botActivityChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
<script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
@endpush

@push('script')
<script>
    "use strict";

    // Auto-refresh every 30 seconds for new messages
    setInterval(function() {
        location.reload();
    }, 30000);
</script>
@endpush
