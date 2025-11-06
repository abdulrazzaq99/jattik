@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('ID')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Income')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branchIncomes as $branchIncome)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showDateTime($branchIncome->date, 'd M Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($branchIncome->totalAmount) }}</span>
                                        </td>
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
