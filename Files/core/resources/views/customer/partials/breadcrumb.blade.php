<div class="row align-items-center mb-30 justify-content-between">
    <div class="col-lg-6 col-sm-6">
        <div class="page-title">
            <h6>{{ __($pageTitle ?? 'Dashboard') }}</h6>
        </div>
    </div>
    <div class="col-lg-6 col-sm-6 text-sm-end mt-sm-0 mt-3 right">
        @stack('breadcrumb-plugins')
    </div>
</div>
