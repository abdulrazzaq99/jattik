@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-warning">
                <div class="card-header bg--warning">
                    <h5 class="card-title text-white mb-0">
                        <i class="las la-exclamation-triangle"></i> @lang('Escalated Conversations')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Phone Number')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Last Message')</th>
                                    <th>@lang('Escalation Reason')</th>
                                    <th>@lang('Messages')</th>
                                    <th>@lang('Escalated At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($escalated as $conversation)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $conversation->phone_number }}</span>
                                        </td>
                                        <td>
                                            @if($conversation->customer)
                                                {{ $conversation->customer->fullname }}<br>
                                                <small class="text-muted">{{ $conversation->customer->email }}</small>
                                            @else
                                                <span class="text-muted">@lang('Guest')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($conversation->last_message)
                                                <small>{{ Str::limit($conversation->last_message->message_content, 50) }}</small><br>
                                                <small class="text-muted">{{ diffForHumans($conversation->last_message->created_at) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($conversation->escalation_reason)
                                                <span class="badge badge--warning">{{ $conversation->escalation_reason }}</span>
                                            @else
                                                <span class="text-muted">@lang('Support Request')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ $conversation->message_count }}</span>
                                        </td>
                                        <td>
                                            {{ showDateTime($conversation->escalated_at) }}<br>
                                            <small class="text-muted">{{ diffForHumans($conversation->escalated_at) }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.whatsapp.conversation', $conversation->phone_number) }}"
                                               class="btn btn-sm btn--primary">
                                                <i class="las la-comments"></i> @lang('Respond')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            <i class="las la-check-circle text--success" style="font-size: 3rem;"></i><br>
                                            @lang('No escalated conversations - Bot handling all queries!')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="las la-exclamation-triangle" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $totalEscalated ?? 0 }}</h3>
                    <p class="mb-0">@lang('Total Escalated')</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="las la-calendar-day" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $todayEscalated ?? 0 }}</h3>
                    <p class="mb-0">@lang('Today')</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="las la-robot" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $botSuccessRate ?? 0 }}%</h3>
                    <p class="mb-0">@lang('Bot Success Rate')</p>
                </div>
            </div>
        </div>
    </div>
@endsection
