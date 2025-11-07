@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-exclamation-circle"></i> @lang('Report an Issue')
                    </h5>
                </div>
                <form action="{{ route('customer.support.issues.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="las la-info-circle"></i>
                            @lang('Please provide as much detail as possible about your issue. This will help us resolve it faster.')
                        </div>

                        {{-- Courier Selection --}}
                        <div class="form-group">
                            <label>@lang('Related Shipment') <span class="text-danger">*</span></label>
                            <select name="courier_info_id" class="form-control" required>
                                <option value="">@lang('Select Shipment')</option>
                                @foreach($recentCouriers as $courier)
                                    <option value="{{ $courier->id }}">
                                        {{ $courier->code }} - {{ $courier->receiverCustomer->fullname ?? 'N/A' }}
                                        ({{ showDateTime($courier->created_at, 'd M Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Issue Type --}}
                        <div class="form-group">
                            <label>@lang('Issue Type') <span class="text-danger">*</span></label>
                            <select name="issue_type" class="form-control" required>
                                <option value="">@lang('Select Issue Type')</option>
                                <option value="wrong_parcel">@lang('Wrong Parcel Delivered')</option>
                                <option value="damaged">@lang('Damaged Package')</option>
                                <option value="missing">@lang('Missing Package')</option>
                                <option value="delay">@lang('Shipment Delay')</option>
                                <option value="other">@lang('Other Issue')</option>
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="form-group">
                            <label>@lang('Description') <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5"
                                      placeholder="Please describe the issue in detail..." required></textarea>
                            <small class="form-text text-muted">@lang('Minimum 20 characters')</small>
                        </div>

                        {{-- Attachments --}}
                        <div class="form-group">
                            <label>@lang('Attachments (Optional)')</label>
                            <input type="file" name="attachments[]" class="form-control" accept="image/*,application/pdf" multiple>
                            <small class="form-text text-muted">
                                @lang('You can upload up to 5 files (images or PDF). Max size: 5MB per file')
                            </small>
                        </div>

                        {{-- Expected Resolution --}}
                        <div class="form-group">
                            <label>@lang('Expected Resolution (Optional)')</label>
                            <textarea name="expected_resolution" class="form-control" rows="3"
                                      placeholder="What outcome would you like?"></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary">
                            <i class="las la-paper-plane"></i> @lang('Submit Issue')
                        </button>
                        <a href="{{ route('customer.support.issues') }}" class="btn btn-outline--secondary">
                            <i class="las la-times"></i> @lang('Cancel')
                        </a>
                    </div>
                </form>
            </div>

            {{-- Help Section --}}
            <div class="card mt-4">
                <div class="card-header bg--success">
                    <h6 class="card-title text-white mb-0">
                        <i class="las la-question-circle"></i> @lang('Frequently Reported Issues')
                    </h6>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    @lang('What should I do if my package is damaged?')
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    @lang('Take photos of the damaged package and its contents immediately. Report the issue with photos attached. Do not discard the packaging.')
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    @lang('How long does it take to resolve an issue?')
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    @lang('Most issues are resolved within 3-5 business days. Priority issues are addressed within 24-48 hours.')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // File upload validation
        $('input[type="file"]').on('change', function() {
            if (this.files.length > 5) {
                alert('@lang("You can upload maximum 5 files")');
                $(this).val('');
                return;
            }

            for (let i = 0; i < this.files.length; i++) {
                if (this.files[i].size > 5242880) { // 5MB
                    alert('@lang("File size should not exceed 5MB")');
                    $(this).val('');
                    return;
                }
            }
        });
    });
</script>
@endpush
