@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-file-invoice-dollar"></i> @lang('File a Claim')
                    </h5>
                </div>
                <form action="{{ route('customer.support.claims.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="las la-info-circle"></i>
                            <strong>@lang('Important:')}</strong> @lang('Claims are processed within 10 business days. Please provide clear evidence to support your claim.')
                        </div>

                        {{-- Related Issue (Optional) --}}
                        <div class="form-group">
                            <label>@lang('Related Issue (Optional)')</label>
                            <select name="support_issue_id" class="form-control">
                                <option value="">@lang('Select Related Issue')</option>
                                @foreach($recentIssues as $issue)
                                    <option value="{{ $issue->id }}">
                                        {{ $issue->issue_number }} - {{ $issue->courierInfo->code ?? 'N/A' }}
                                        ({{ ucwords(str_replace('_', ' ', $issue->issue_type)) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">@lang('Link this claim to an existing issue if applicable')</small>
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

                        {{-- Claim Type --}}
                        <div class="form-group">
                            <label>@lang('Claim Type') <span class="text-danger">*</span></label>
                            <select name="claim_type" class="form-control" required>
                                <option value="">@lang('Select Claim Type')</option>
                                <option value="damage">@lang('Damage - Package arrived damaged')</option>
                                <option value="loss">@lang('Loss - Package lost or not received')</option>
                                <option value="delay_compensation">@lang('Delay Compensation - Significant delivery delay')</option>
                                <option value="other">@lang('Other Claim')</option>
                            </select>
                        </div>

                        {{-- Claimed Amount --}}
                        <div class="form-group">
                            <label>@lang('Claimed Amount') <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">{{ gs()->cur_sym }}</span>
                                <input type="number" name="claimed_amount" class="form-control"
                                       step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <small class="form-text text-muted">@lang('Enter the compensation amount you are claiming')</small>
                        </div>

                        {{-- Description --}}
                        <div class="form-group">
                            <label>@lang('Claim Description') <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5"
                                      placeholder="Describe what happened and why you are filing this claim..." required></textarea>
                            <small class="form-text text-muted">@lang('Minimum 50 characters. Be as detailed as possible.')</small>
                        </div>

                        {{-- Evidence Files --}}
                        <div class="form-group">
                            <label>@lang('Evidence (Required)') <span class="text-danger">*</span></label>
                            <input type="file" name="evidence[]" class="form-control" accept="image/*,application/pdf" multiple required>
                            <small class="form-text text-muted">
                                @lang('Upload photos/documents as evidence. You can upload up to 10 files. Max size: 10MB per file.')
                                <br>
                                @lang('For damage claims: Photos of damaged package and contents')
                                <br>
                                @lang('For loss claims: Proof of non-delivery')
                                <br>
                                @lang('For delay claims: Original delivery schedule and actual delivery date')
                            </small>
                        </div>

                        {{-- Expected Resolution --}}
                        <div class="form-group">
                            <label>@lang('Expected Resolution')</label>
                            <textarea name="expected_resolution" class="form-control" rows="3"
                                      placeholder="What outcome would you like? (e.g., full refund, replacement, partial compensation)"></textarea>
                        </div>

                        {{-- Terms Agreement --}}
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="agree_terms" name="agree_terms" value="1" required>
                                <label class="custom-control-label" for="agree_terms">
                                    @lang('I certify that all information provided is accurate and I understand that false claims may result in account suspension.')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary">
                            <i class="las la-paper-plane"></i> @lang('Submit Claim')
                        </button>
                        <a href="{{ route('customer.support.claims') }}" class="btn btn-outline--secondary">
                            <i class="las la-times"></i> @lang('Cancel')
                        </a>
                    </div>
                </form>
            </div>

            {{-- SLA Information Card --}}
            <div class="card mt-4 border-primary">
                <div class="card-header bg--primary">
                    <h6 class="card-title text-white mb-0">
                        <i class="las la-clock"></i> @lang('Claims Processing Timeline')
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <i class="las la-check-circle text--success"></i>
                            <strong>Day 0:</strong> Claim submitted and assigned unique claim number
                        </div>
                        <div class="timeline-item mt-2">
                            <i class="las la-search text--info"></i>
                            <strong>Days 1-3:</strong> Initial review by support team
                        </div>
                        <div class="timeline-item mt-2">
                            <i class="las la-clipboard-check text--warning"></i>
                            <strong>Days 4-7:</strong> Investigation and evidence verification
                        </div>
                        <div class="timeline-item mt-2">
                            <i class="las la-dollar-sign text--success"></i>
                            <strong>Days 8-10:</strong> Decision made and compensation processed
                        </div>
                    </div>
                    <hr>
                    <p class="mb-0 small text-muted">
                        <i class="las la-info-circle"></i>
                        @lang('You will receive email notifications at each stage. Approved claims are paid within 5 business days.')
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .timeline-item {
        padding-left: 30px;
        position: relative;
    }
    .timeline-item i {
        position: absolute;
        left: 0;
        top: 2px;
        font-size: 1.2rem;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        // File upload validation
        $('input[type="file"]').on('change', function() {
            if (this.files.length > 10) {
                alert('@lang("You can upload maximum 10 files")');
                $(this).val('');
                return;
            }

            for (let i = 0; i < this.files.length; i++) {
                if (this.files[i].size > 10485760) { // 10MB
                    alert('@lang("File size should not exceed 10MB")');
                    $(this).val('');
                    return;
                }
            }
        });

        // Update required evidence based on claim type
        $('select[name="claim_type"]').on('change', function() {
            let type = $(this).val();
            let helpText = '';

            if (type === 'damage') {
                helpText = '@lang("Required: Photos showing damaged packaging and contents")';
            } else if (type === 'loss') {
                helpText = '@lang("Required: Proof that package was not delivered (e.g., delivery confirmation showing wrong address)")';
            } else if (type === 'delay_compensation') {
                helpText = '@lang("Required: Original delivery schedule and actual delivery date documentation")';
            }

            if (helpText) {
                $('.evidence-help').html('<div class="alert alert-info mt-2">' + helpText + '</div>');
            }
        });
    });
</script>
@endpush
