@if($customFields)
<h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

<!-- Restaurant Id Field -->
<div class="form-group row ">
  {!! Form::label('restaurant_id', 'Restaurant' ,['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::select('restaurant_id', $restaurant, null, ['class' => 'select2 form-control']) !!}
    <div class="form-text text-muted">Select Restaurant</div>
  </div>
</div>


<!-- Payout History Field -->
<div class="form-group row ">
  {!! Form::label('payout_period', 'Payout Period', ['class' => 'col-3 control-label text-right']) !!}

  <div class="col-9">
    <input class="form-control" name="amount" type="text" id="txtPayoutPeriod" readonly> 
    <div class="form-text text-muted">Select Payout Period</div>
  </div>

</div>


<!-- Amount Field -->
<div class="form-group row ">
  {!! Form::label('amount', trans("lang.restaurants_payout_amount"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
      {!! Form::text('amount', null,  ['readonly' => 'true', 'class' => 'form-control', 'step'=>"any" ]) !!}
    <div class="form-text text-muted"></div>
  </div>
</div>


</div>

<input type="hidden" id="startDate" name="startDate">
<input type="hidden" id="endDate" name="endDate">
<input type="hidden" id="payoutAmount" name="payoutAmount">


<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

<!-- Note Field -->
<div class="form-group row ">
  {!! Form::label('note', trans("lang.restaurants_payout_note"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::textarea('note', null, ['class' => 'form-control','placeholder' => trans("lang.restaurants_payout_note_placeholder")  ]) !!}
    <div class="form-text text-muted">{{ trans("lang.restaurants_payout_note_help") }}</div>
  </div>
</div>
</div>
@if($customFields)
<div class="clearfix"></div>
<div class="col-12 custom-field-container">
  <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
  {!! $customFields !!}
</div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 text-right">
  <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.restaurants_payout')}}</button>
  <a href="{!! route('restaurantsPayouts.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>

@push('css_lib')
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payout.css') }}">
@endpush

@push('scripts_lib')
    <script type="text/javascript" src="{{ asset('plugins/daterangepicker/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script type="module" src="{{ asset('js/payout.js') }}"></script>
@endpush
