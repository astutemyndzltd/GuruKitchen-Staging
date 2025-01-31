<!-- Id Field -->
<div class="form-group row col-md-4 col-sm-12">

  {!! Form::label('id', trans('lang.order_id'), ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    <p>#{!! $order->id !!}</p>
  </div>

  {!! Form::label('order_client', trans('lang.order_client'), ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    <p>{!! $order->user->name !!}</p>
  </div>

  {!! Form::label('order_client_phone', trans('lang.order_client_phone'), ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    <p>{!! isset($order->user->custom_fields['phone']) ? $order->user->custom_fields['phone']['view'] : "" !!}</p>
  </div>


  @if($order->order_type == 'Delivery')
    {!! Form::label('delivery_address', trans('lang.delivery_address'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
      <p>{!! $order->deliveryAddress ? $order->deliveryAddress->address : '' !!}</p>
    </div>
  @endif


  {!! Form::label('order_date', trans('lang.order_date'), ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    <p>{!! $order->created_at !!}</p>
  </div>

</div>

<!-- Order Status Id Field -->
<div class="form-group row col-md-4 col-sm-12">

  {!! Form::label('order_status_id', trans('lang.order_status_status'), ['class' => 'col-4 control-label']) !!}

  <?php $colors = ['blue', 'green', 'orange', 'orange', 'red', 'red'];
        $statusId = $order->active ? $order->order_status_id : 0;
        $status = $order->active ? $order->orderStatus->status : 'Cancelled'; ?>

  <div class="col-8">
    <p style="color:{{ $colors[$statusId] }}"><b>{!! $status !!}</b></p>
  </div>

  <?php
  /*
  {!! Form::label('active', trans('lang.order_active'), ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    @if($order->active)
    <p><span class='badge badge-success'> {{trans('lang.yes')}}</span></p>
    @else
    <p><span class='badge badge-danger'>{{trans('lang.order_canceled')}}</span></p>
    @endif

  </div> 
  */ 
  ?>


  @if($order->active)
  @endif

  {!! Form::label('payment_method', 'Card', ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    <p>{!! isset($order->payment) ? $order->payment->method : '' !!}</p>
  </div>

  {!! Form::label('payment_status', trans('lang.payment_status'), ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    <p>{!! isset($order->payment) ? $order->payment->status : trans('lang.order_not_paid') !!}</p>
  </div>
  {!! Form::label('order_updated_date', trans('lang.order_updated_at'), ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    <p>{!! $order->updated_at !!}</p>
  </div>

</div>

<!-- Id Field -->
<div class="form-group row col-md-4 col-sm-12">
  {!! Form::label('restaurant', trans('lang.restaurant'), ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    @if(isset($order->foodOrders[0]))
    <p>{!! $order->foodOrders[0]->food->restaurant->name !!}</p>
    @endif
  </div>


  {!! Form::label('order_type', 'Order Type', ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    <p>{!! $order->order_type !!}</p>
  </div>

  {{-- {!! Form::label('restaurant_address', trans('lang.restaurant_address'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
        @if(isset($order->foodOrders[0]))
            <p>{!! $order->foodOrders[0]->food->restaurant->address !!}</p>
        @endif
    </div>  --}}

  {!! Form::label('restaurant_phone', trans('lang.restaurant_phone'), ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    @if(isset($order->foodOrders[0]))
    <p>{!! $order->foodOrders[0]->food->restaurant->phone !!}</p>
    @endif
  </div>

 @if($order->order_type != 'Pickup')
  {!! Form::label('driver', trans('lang.driver'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
      @if(isset($order->driver))
      <p>{!! $order->driver->name !!}</p>
      @else
      <p>{{trans('lang.order_driver_not_assigned')}}</p>
      @endif
    </div>
 @endif

  {!! Form::label('hint', 'Hint', ['class' => 'col-4 control-label']) !!}
  <div class="col-8">
    <p>{!! $order->hint !!}</p>
  </div>

</div>

<?php 
  $useAppDrivers = $order->foodOrders[0]->food->restaurant->use_app_drivers;
  $usingGkDriverForOrder = $order->use_app_drivers; 
?>

@if($useAppDrivers && $usingGkDriverForOrder)
  <!-- Using App Drivers -->
  <div class="form-group row col-md-8 col-sm-12">
    {!! Form::label('use_app_driver', 'Used GuruKitchen Driver', ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
      <p>{!! $usingGkDriverForOrder ? 'Yes' : 'No' !!}</p>
    </div>
  </div>
@endif


@if(auth()->user()->hasRole('admin') && isset($order->payment->transaction_id))
<!-- Transaction ID -->
<div class="form-group row col-md-8 col-sm-12">
  {!! Form::label('transaction_id', 'Transaction ID', ['class' => 'col-3 control-label']) !!}
  <div class="col-8">
    <p>{!! $order->payment->transaction_id !!}</p>
  </div>
</div>
@endif


@if($order->preorder_info != null || $order->preorder_info != '')
<!-- Pre-Order data -->
<div class="form-group row col-md-8 col-sm-12">
  {!! Form::label('preorder_info', 'Pre-Order', ['class' => 'col-2 control-label']) !!}
  <div class="col-9">
    <p style="text-align:justify;">Expected by {!! $order->preorder_info !!}</p>
  </div>
</div>
@endif



<div class="form-group row col-md-12 col-sm12">
  {!! Form::label('note', 'Customer Note', ['class' => 'col-2 control-label']) !!}
  <div class="col-9">
    <p style="text-align:justify;">{!! $order->note !!}</p>
  </div>
</div>



{{--<!-- Tax Field -->--}}
{{--<div class="form-group row col-md-6 col-sm-12">--}}
{{-- {!! Form::label('tax', 'Tax:', ['class' => 'col-4 control-label']) !!}--}}
{{-- <div class="col-8">--}}
{{-- <p>{!! $order->tax !!}</p>--}}
{{-- </div>--}}
{{--</div>--}}