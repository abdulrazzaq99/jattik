@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header bg--success">
                    <h5 class="card-title text-white mb-0">
                        <i class="lab la-whatsapp"></i> @lang('Conversation with') {{ $phoneNumber }}
                    </h5>
                </div>
                <div class="card-body chat-container" style="height: 500px; overflow-y: auto;">
                    @forelse($messages as $message)
                        <div class="message-bubble mb-3 {{ $message->direction == 'outbound' ? 'text-end' : '' }}">
                            <div class="d-inline-block p-3 rounded
                                {{ $message->direction == 'outbound' ? 'bg-primary text-white' : 'bg-light' }}"
                                style="max-width: 70%;">
                                {{-- Message Type Indicator --}}
                                @if($message->message_type == 'otp')
                                    <small class="badge badge--warning mb-1">@lang('OTP')</small>
                                @elseif($message->message_type == 'order_update')
                                    <small class="badge badge--info mb-1">@lang('Order Update')</small>
                                @elseif($message->message_type == 'faq_response')
                                    <small class="badge badge--primary mb-1">@lang('FAQ Response')</small>
                                @endif

                                {{-- Message Content --}}
                                <p class="mb-1">{{ $message->message_content }}</p>

                                {{-- Message Meta --}}
                                <small class="{{ $message->direction == 'outbound' ? 'text-white-50' : 'text-muted' }}">
                                    {{ showDateTime($message->created_at, 'd M Y, h:i A') }}
                                    @if($message->is_sent)
                                        <i class="las la-check-double"></i>
                                    @endif
                                </small>

                                {{-- Bot Intent Badge --}}
                                @if($message->bot_intent)
                                    <br><small class="badge badge--info mt-1">{{ $message->bot_intent }}</small>
                                @endif

                                {{-- Escalation Notice --}}
                                @if($message->escalated_to_human)
                                    <br><small class="badge badge--warning mt-1">
                                        <i class="las la-user-tie"></i> @lang('Escalated to Human')
                                    </small>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="lab la-whatsapp" style="font-size: 4rem;"></i>
                            <p>@lang('No messages in this conversation yet')</p>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer">
                    {{-- Customer Info (if available) --}}
                    @if($customer)
                        <div class="mb-3">
                            <strong>@lang('Customer'):</strong> {{ $customer->fullname }}
                            ({{ $customer->email }})
                        </div>
                    @endif

                    {{-- Response Form --}}
                    <form action="{{ route('admin.whatsapp.respond') }}" method="POST">
                        @csrf
                        <input type="hidden" name="phone_number" value="{{ $phoneNumber }}">
                        <div class="input-group">
                            <textarea name="message" class="form-control" rows="2"
                                      placeholder="Type your message..." required></textarea>
                            <button type="submit" class="btn btn--success">
                                <i class="las la-paper-plane"></i> @lang('Send')
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            @lang('This will be sent via WhatsApp to') {{ $phoneNumber }}
                        </small>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .chat-container {
        background: #f5f5f5;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e0e0e0' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .message-bubble {
        animation: fadeIn 0.3s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        // Scroll to bottom of chat
        $('.chat-container').scrollTop($('.chat-container')[0].scrollHeight);

        // Auto-refresh every 10 seconds
        setInterval(function() {
            location.reload();
        }, 10000);
    });
</script>
@endpush
