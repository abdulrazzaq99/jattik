@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        {{-- Configuration Status --}}
        <div class="col-lg-12 mb-4">
            @if($isConfigured)
                <div class="alert alert-success">
                    <h5><i class="las la-check-circle"></i> @lang('WhatsApp Bot is Configured')</h5>
                    <p class="mb-0">@lang('Your WhatsApp bot is ready to receive and send messages.')</p>
                </div>
            @else
                <div class="alert alert-warning">
                    <h5><i class="las la-exclamation-triangle"></i> @lang('WhatsApp Bot Not Configured')</h5>
                    <p class="mb-0">@lang('Please add your WhatsApp API credentials to enable the bot.')</p>
                </div>
            @endif
        </div>

        {{-- Statistics Cards --}}
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="lab la-whatsapp text--success" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-3">{{ $stats['total_messages'] }}</h3>
                    <p class="text-muted mb-0">@lang('Total Messages')</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="las la-arrow-up text--primary" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-3">{{ $stats['outbound_messages'] }}</h3>
                    <p class="text-muted mb-0">@lang('Outbound')</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="las la-arrow-down text--info" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-3">{{ $stats['inbound_messages'] }}</h3>
                    <p class="text-muted mb-0">@lang('Inbound')</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="las la-key text--warning" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-3">{{ $stats['otps_sent'] }}</h3>
                    <p class="text-muted mb-0">@lang('OTPs Sent')</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        {{-- Webhook Configuration --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg--primary">
                    <h5 class="card-title text-white mb-0">
                        <i class="las la-link"></i> @lang('Webhook Configuration')
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>@lang('Webhook URL')</label>
                        <div class="input-group">
                            <input type="text" class="form-control" readonly
                                   value="{{ route('webhook.whatsapp') }}" id="webhookUrl">
                            <button class="btn btn--primary" onclick="copyWebhookUrl()">
                                <i class="las la-copy"></i> @lang('Copy')
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            @lang('Configure this URL in your Meta/WhatsApp Business API dashboard')
                        </small>
                    </div>

                    <div class="form-group">
                        <label>@lang('Verify Token')</label>
                        <input type="text" class="form-control" readonly
                               value="{{ config('services.whatsapp.verify_token', 'your_verify_token') }}">
                        <small class="form-text text-muted">
                            @lang('Use this token when setting up the webhook in Meta dashboard')
                        </small>
                    </div>

                    <div class="alert alert-info mt-3">
                        <strong>@lang('Setup Steps:')}</strong>
                        <ol class="mb-0 ps-3">
                            <li>@lang('Go to Meta for Developers')</li>
                            <li>@lang('Select your WhatsApp Business App')</li>
                            <li>@lang('Configure Webhooks with above URL and token')</li>
                            <li>@lang('Subscribe to message events')</li>
                            <li>@lang('Add access token to .env file')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bot Configuration --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg--success">
                    <h5 class="card-title text-white mb-0">
                        <i class="las la-robot"></i> @lang('Bot Configuration')
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>@lang('Phone Number ID')</label>
                        <input type="text" class="form-control"
                               value="{{ config('services.whatsapp.phone_number_id', 'Not configured') }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>@lang('Access Token Status')</label>
                        <div>
                            @if($isConfigured)
                                <span class="badge badge--success">
                                    <i class="las la-check-circle"></i> @lang('Configured')
                                </span>
                            @else
                                <span class="badge badge--danger">
                                    <i class="las la-times-circle"></i> @lang('Not Configured')
                                </span>
                            @endif
                        </div>
                        <small class="form-text text-muted">
                            @lang('Configure in .env: WHATSAPP_ACCESS_TOKEN')
                        </small>
                    </div>

                    <hr>

                    <h6>@lang('Bot Capabilities')</h6>
                    <ul class="list-unstyled">
                        <li><i class="las la-check text--success"></i> @lang('Send OTP codes')</li>
                        <li><i class="las la-check text--success"></i> @lang('Track shipments')</li>
                        <li><i class="las la-check text--success"></i> @lang('Answer FAQs')</li>
                        <li><i class="las la-check text--success"></i> @lang('Escalate to human support')</li>
                        <li><i class="las la-check text--success"></i> @lang('Send order updates')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Quick Actions')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('admin.whatsapp.conversations') }}" class="btn btn-outline--primary w-100 mb-2">
                                <i class="las la-comments"></i> @lang('View Conversations')
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.whatsapp.escalated') }}" class="btn btn-outline--warning w-100 mb-2">
                                <i class="las la-exclamation-triangle"></i> @lang('Escalated Issues')
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.whatsapp.messages') }}" class="btn btn-outline--info w-100 mb-2">
                                <i class="las la-list"></i> @lang('All Messages')
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="https://developers.facebook.com/docs/whatsapp/cloud-api" target="_blank"
                               class="btn btn-outline--secondary w-100 mb-2">
                                <i class="las la-book"></i> @lang('API Documentation')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    function copyWebhookUrl() {
        var copyText = document.getElementById("webhookUrl");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");

        iziToast.success({
            message: "Webhook URL copied to clipboard!",
            position: "topRight"
        });
    }
</script>
@endpush
