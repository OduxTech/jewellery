@extends('layouts.app')
@section('title', __('report.serial_stock_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('report.serial_stock_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getSerialStockReport']), 'method' => 'get', 'id' => 'stock_report_filter_form' ]) !!}
                <!-- Quick Filter Buttons -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Quick Filters:</label>
                        <button type="button" id="last_week_btn" class="btn btn-primary btn-sm">Last 7 Days</button>
                        <button type="button" id="last_month_btn" class="btn btn-default btn-sm">Last 30 Days</button>
                        <button type="button" id="all_time_btn" class="btn btn-default btn-sm">All Time</button>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('category_id', __('category.category') . ':') !!}
                        {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('brand', __('product.brand') . ':') !!}
                        {!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('status', 'Status:') !!}
                        {!! Form::select('status', [
                            '' => __('messages.all'),
                            'available' => 'Available',
                            'sold' => 'Sold', 
                            'returned' => 'Returned'
                        ], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'status']); !!}
                    </div>
                </div>
                <!-- DATE FILTERS -->
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('start_date', 'From Date:') !!}
                        {!! Form::text('start_date', \Carbon\Carbon::now()->subDays(7)->format('Y-m-d'), ['class' => 'form-control datepicker', 'readonly', 'placeholder' => 'YYYY-MM-DD', 'id' => 'start_date']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('end_date', 'To Date:') !!}
                        {!! Form::text('end_date', \Carbon\Carbon::now()->format('Y-m-d'), ['class' => 'form-control datepicker', 'readonly', 'placeholder' => 'YYYY-MM-DD', 'id' => 'end_date']); !!}
                    </div>
                </div>
                <!-- PURCHASE REF NO FILTER -->
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('purchase_ref_no', 'Purchase Ref No:') !!}
                        {!! Form::text('purchase_ref_no', null, ['class' => 'form-control', 'placeholder' => 'e.g., PO2025/0024', 'id' => 'purchase_ref_no']); !!}
                    </div>
                </div>
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid'])
                @include('report.partials.serial_stock_report_table')
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . time()) }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize datepicker
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            // Quick filter buttons
            $('#last_week_btn').on('click', function() {
                var startDate = new Date();
                startDate.setDate(startDate.getDate() - 7);
                var endDate = new Date();
                
                $('#start_date').val(startDate.toISOString().split('T')[0]);
                $('#end_date').val(endDate.toISOString().split('T')[0]);
                $('#serial_stock_report_table').DataTable().ajax.reload();
            });

            $('#last_month_btn').on('click', function() {
                var startDate = new Date();
                startDate.setDate(startDate.getDate() - 30);
                var endDate = new Date();
                
                $('#start_date').val(startDate.toISOString().split('T')[0]);
                $('#end_date').val(endDate.toISOString().split('T')[0]);
                $('#serial_stock_report_table').DataTable().ajax.reload();
            });

            $('#all_time_btn').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#serial_stock_report_table').DataTable().ajax.reload();
            });
        });
    </script>
@endsection