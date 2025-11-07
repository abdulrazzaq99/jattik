@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="lab la-whatsapp"></i> @lang('All WhatsApp Messages')
                    </h5>
                    <div>
                        <a href="{{ route('admin.whatsapp.conversations') }}" class="btn btn-sm btn-outline--secondary">
                            <i class="las la-comments"></i> @lang('Conversations')
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
                                    <th>@lang('Direction')</th>
                                    <th>@lang('Phone Number')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Message Type')</th>
                                    <th>@lang('Content')</th>
                                    <th>@lang('Bot Intent')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Time')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $message)
                                    <tr class="{{ $message->direction == 'inbound' ? 'bg-light' : '' }}">
                                        <td>
                                            @if($message->direction == 'inbound')
                                                <span class="badge badge--info">
                                                    <i class="las la-arrow-down"></i> @lang('Inbound')
                                                </span>
                                            @else
                                                <span class="badge badge--success">
                                                    <i class="las la-arrow-up"></i> @lang('Outbound')
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $message->phone_number }}</span>
                                        </td>
                                        <td>
                                            @if($message->customer)
                                                {{ $message->customer->fullname }}<br>
                                                <small class="text-muted">{{ $message->customer->email }}</small>
                                            @else
                                                <span class="text-muted">@lang('Guest')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($message->message_type == 'otp')
                                                <span class="badge badge--warning">@lang('OTP')</span>
                                            @elseif($message->message_type == 'order_update')
                                                <span class="badge badge--info">@lang('Order Update')</span>
                                            @elseif($message->message_type == 'faq_response')
                                                <span class="badge badge--primary">@lang('FAQ')</span>
                                            @else
                                                <span class="badge badge--secondary">@lang('Text')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($message->message_content, 60) }}</small>
                                        </td>
                                        <td>
                                            @if($message->bot_intent)
                                                <span class="badge badge--info">{{ $message->bot_intent }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($message->escalated_to_human)
                                                <span class="badge badge--warning">
                                                    <i class="las la-user-tie"></i> @lang('Escalated')
                                                </span>
                                            @elseif($message->otp_verified)
                                                <span class="badge badge--success">@lang('Verified')</span>
                                            @elseif($message->is_sent)
                                                <span class="badge badge--success">@lang('Sent')</span>
                                            @else
                                                <span class="badge badge--secondary">@lang('Received')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($message->created_at) }}<br>
                                            <small class="text-muted">{{ diffForHumans($message->created_at) }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            @lang('No messages found')
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
@endsection
