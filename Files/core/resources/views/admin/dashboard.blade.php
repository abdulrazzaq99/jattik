@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.courier.info.index'). '?status=0' }}" icon="las la-hourglass-start" title="{{ __('Sent In Queue') }}"
                value="{{ $sentInQueue }}" bg="purple" outline="true"/>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.courier.info.index') . '?status=1' }}" icon="las la-dolly" title="{{ __('Shipped Courier') }}"
                value="{{ $shippingCourier }}" bg="green" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.courier.info.index'). '?status=2' }}" icon="lab la-accessible-icon"
                title="{{ __('Waiting for Delivery') }}" value="{{ $deliveryInQueue }}" bg="deep-purple" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.courier.info.index') . '?status=3' }}" icon="las  la-list-alt"
                title="{{ __('Delivered') }}" value="{{ $delivered }}" bg="info" outline="true" />
        </div>
    </div>

    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.branch.index') }}" title="{{ __('Total Branch') }}" icon="las la-university" value="{{ $branchCount }}" bg="primary" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.branch.manager.index') }}" title="{{ __('Total manager') }}" icon="las la-user-check" value="{{ $managerCount }}" bg="cyan" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.courier.branch.income') }}" title="{{ __('Total Income') }}" icon="las la-money-bill-wave" value="{{ showAmount($totalIncome) }}" bg="orange" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.courier.info.index') }}" title="{{ __('Total Courier') }}" icon="las la-dolly-flatbed" value="{{ $courierInfoCount }}" bg="pink"
                outline="true" />
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name - Address')</th>
                                    <th>@lang('Email - Phone')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branches as $branch)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __($branch->name) }}</span><br>
                                            <span>{{ __($branch->address) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $branch->email }}</span><br>
                                            <span>{{ $branch->phone }}</span>
                                        </td>
                                        <td> @php echo $branch->statusBadge; @endphp </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary" id="actionButton"
                                                data-bs-toggle="dropdown"><i class="las la-ellipsis-v"></i>
                                                @lang('Action')</button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('admin.branch.manager.list', $branch->id) }}"
                                                    class="dropdown-item"><i class="las la-user-tie"></i>
                                                    @lang('Manager')
                                                </a>
                                                <a href="{{ route('admin.courier.info.index') . '?&sender_branch_id=' . $branch->id }}"
                                                    class=" dropdown-item"><i class="las la-dolly-flatbed"></i>
                                                    @lang('Courier')
                                                </a>
                                                <a href="{{ route('admin.courier.info.index') . '?status=3&receiver_branch_id=' . $branch->id }}"
                                                    class="dropdown-item"><i class="las la-truck"></i>
                                                    @lang('Delivery')
                                                </a>
                                            </div>
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
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-4 col-lg-6 ">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Browser') (@lang('Last 30 days'))</h5>
                    <canvas id="userBrowserChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By OS') (@lang('Last 30 days'))</h5>
                    <canvas id="userOsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Country') (@lang('Last 30 days'))</h5>
                    <canvas id="userCountryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/viseradmin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/viseradmin/js/charts.js') }}"></script>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict"

            piChart(
                document.getElementById('userBrowserChart'),
                @json(@$chart['user_browser_counter']->keys()),
                @json(@$chart['user_browser_counter']->flatten())
            );

            piChart(
                document.getElementById('userOsChart'),
                @json(@$chart['user_os_counter']->keys()),
                @json(@$chart['user_os_counter']->flatten())
            );

            piChart(
                document.getElementById('userCountryChart'),
                @json(@$chart['user_country_counter']->keys()),
                @json(@$chart['user_country_counter']->flatten())
            );
        })(jQuery)
    </script>
@endpush
