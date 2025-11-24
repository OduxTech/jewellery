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
                        {!! Form::text('start_date', null, ['class' => 'form-control datepicker', 'readonly', 'placeholder' => 'YYYY-MM-DD']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('end_date', 'To Date:') !!}
                        {!! Form::text('end_date', null, ['class' => 'form-control datepicker', 'readonly', 'placeholder' => 'YYYY-MM-DD']); !!}
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
        });
    </script>
@endsection