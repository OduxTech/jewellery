<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="tw-rounded-lg tw-shadow-lg tw-p-4 tw-text-white" style="background: linear-gradient(135deg, #FFD700 0%, #B8860B 100%);
                box-shadow: 0 4px 10px rgba(184, 134, 11, 0.6);
                border: 2px solid #DAA520;">


      {!! Form::open(['url' => action([\App\Http\Controllers\GoldRateController::class, 'store']), 'method' => 'post', 'id' => $quick_add ? 'quick_add_gold_rate_form' : 'gold_rate_add_form']) !!}

      <!-- Modal Header -->
      <div class="modal-header border-0">
        <button type="button" class="close tw-text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title tw-text-2xl tw-font-bold">
          Update Gold and Silver Prices
        </h4>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">

        <!-- 24 Carat Gold Price -->
<div class="form-group tw-mb-4">
  {!! Form::label('gold_price', '24 Carat Gold Price:*', ['class' => 'tw-text-lg tw-font-semibold tw-text-black']) !!}
  {!! Form::number('gold_price', isset($latestRates[1]) ? number_format($latestRates[1]->price, 2, '.', '') : '', [
    'class' => 'form-control tw-text-black tw-rounded-md tw-py-2 tw-px-3',
    'required',
    'step' => '0.01',
    'placeholder' => 'Enter 24 Carat Gold Price'
  ]) !!}
</div>

<!-- Silver Price -->
<div class="form-group tw-mb-4">
  {!! Form::label('silver_price', 'Silver Price:*', ['class' => 'tw-text-lg tw-font-semibold tw-text-black']) !!}
  {!! Form::number('silver_price', isset($latestRates[3]) ? number_format($latestRates[3]->price, 2, '.', '') : '', [
    'class' => 'form-control tw-text-black tw-rounded-md tw-py-2 tw-px-3',
    'required',
    'step' => '0.01',
    'placeholder' => 'Enter Silver Price'
  ]) !!}
</div>


      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.update')</button>
        <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
          data-dismiss="modal">@lang('messages.close')</button>
      </div>



      {!! Form::close() !!}
    </div>
  </div>
</div>