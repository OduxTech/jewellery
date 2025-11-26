@php
  $custom_labels = json_decode(session('business.custom_labels'), true);
  $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
  $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : __('lang_v1.product_custom_field2');
  $product_custom_field3 = !empty($custom_labels['product']['custom_field_3']) ? $custom_labels['product']['custom_field_3'] : __('lang_v1.product_custom_field3');
  $product_custom_field4 = !empty($custom_labels['product']['custom_field_4']) ? $custom_labels['product']['custom_field_4'] : __('lang_v1.product_custom_field4');
@endphp
<table class="table table-bordered table-striped" id="serial_stock_report_table">
    <thead>
        <tr>
            <th>Serial Number</th>
            <th>Status</th>
            <th>Sold Date</th> <!-- ADD THIS COLUMN -->
            <th>SKU</th>
            <th>@lang('business.product')</th>
            <th>Caret Value</th>
            <th>Weight</th>
            <th>@lang('product.category')</th>
            <th>@lang('sale.location')</th>
            <th>Supplier</th>
            <th>Purchase Ref No</th> <!-- ADD PURCHASE REFERENCE NUMBER COLUMN -->
            <th>@lang('purchase.unit_selling_price')</th>
            <th>@lang('report.current_stock')</th>
        </tr>
    </thead>
</table>