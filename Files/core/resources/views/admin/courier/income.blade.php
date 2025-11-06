@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Branch')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Income')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branchIncomes as $branchIncome)
                                    <tr>
                                        <td>{{ __(@$branchIncome->branch->name) }}</td>
                                        <td>{{ showDateTime($branchIncome->date, 'd M Y') }}</td>
                                        <td>{{ showAmount($branchIncome->totalAmount) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($branchIncomes->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($branchIncomes) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <form id="branchFilter">
        <select class="form-control select2" name="branch_id" data-minimum-results-for-search="-1">
            <option value="">@lang('Select Branch')</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(request()->branch_id == $branch->id)>
                    {{ __($branch->name) }}
                </option>
            @endforeach
        </select>
    </form>

    <x-search-date-field placeholder="Start date - End date" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            $('select[name="branch_id"]').on('change', function(e) {
                e.preventDefault();
                $('#branchFilter').submit();
            })
        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .select2-container {
            min-width: 200px;
        }
    </style>
@endpush
