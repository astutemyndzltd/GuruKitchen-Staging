@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{trans('lang.order_plural')}}<small class="ml-3 mr-3">|</small><small>{{trans('lang.order_desc')}}</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
          <li class="breadcrumb-item"><a href="{!! route('orders.index') !!}">{{trans('lang.order_plural')}}</a>
          </li>
          <li class="breadcrumb-item active">{{trans('lang.order')}}</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div class="content">
  <div class="card">
    <div class="card-header d-print-none">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        <li class="nav-item">
          <a class="nav-link" href="{!! route('orders.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.order_table')}}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.order')}}</a>
        </li>
        <div class="ml-auto d-inline-flex">
          <li class="nav-item">
            <a class="nav-link pt-1" id="printOrder" href="#"><i class="fa fa-print"></i> {{trans('lang.print')}}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link pt-1" id="printOrderWithStar" href="#"><i class="fa fa-print"></i> Print to STAR Printer </a>
          </li>
        </div>
      </ul>
    </div>
    <div class="card-body">
      <div class="row">
        @include('orders.show_fields')
      </div>
      @include('food_orders.table')
      <div class="row">
        <div class="col-5 offset-7">
          <div class="table-responsive table-light">
            <table class="table">

              <tbody>
                <tr>
                  <th class="text-right">{{trans('lang.order_subtotal')}}</th>
                  <td>{!! getPrice($subtotal) !!}</td>
                </tr>
                <tr>
                  <th class="text-right">{{trans('lang.order_delivery_fee')}}</th>
                  <td>{!! getPrice($order['delivery_fee']) !!}</td>
                </tr>
                {{-- <tr>
                  <th class="text-right">{{trans('lang.order_tax')}} ({!!$order->tax!!}%) </th>
                <td>{!! getPrice($taxAmount)!!}</td>
                </tr> --}}

                <tr>
                  <th class="text-right">{{trans('lang.order_total')}}</th>
                  <td>{!! getPrice($total) !!}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="row d-print-none">
        <!-- Back Field -->
        <div class="form-group col-12 text-right">
          <a href="{!! route('orders.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.back')}}</a>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>

    <!--<link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('plugins/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/all.css') }}">
        <link rel="stylesheet" href="{{ asset('css/receipt.css') }}">-->

    <div id="receipt-head" style="display:none;">
      
      <style>
        @media print and (min-width: 5cm) {
          #receipt {
            transform: scale(1.38) !important;
            transform-origin: 0 0 !important;
          }
        }


        @media print {

          @page {
            margin: 0;
          }


          html,
          body {
            margin: 0;
            /*all: unset;*/
          }

          body * {
            visibility: hidden;
          }

          #receipt,
          #receipt * {
            visibility: visible;
          }

          #receipt {
            transform: scale(1);
            display: block !important;
            -webkit-print-color-adjust: exact !important;
            /*position: absolute;
    	left: 0;
    	top: 0;*/
          }
        }

        #receipt {
          font-family: 'Poppins', sans-serif;
          width: 58mm;
          padding-bottom: 50px;
          line-height: normal;
          color: black;
          /* these 3 were in print section */
          position: absolute;
          left: 0;
          top: 0;
          z-index: -100000000;
        }



        #receipt #logo {
          width: 50mm;
          height: 50mm;
          display: block;
          margin: 0px auto;
        }

        #receipt #order-id {
          text-align: center;
          margin-top: 0px;
          margin-bottom: 15px;
        }

        #receipt .intro-row {
          padding: 0px 14px;
          display: flex;
          align-items: center;
          min-height: 27px;
          margin-bottom: 5px;
        }

        #receipt .intro-row>i {
          width: 16px;
          height: 16px;
          flex-grow: 0;
          flex-shrink: 0;
        }

        #receipt .intro-row>span {
          font-size: 12px;
          text-align: left;
          margin-left: 10px;
        }

        #receipt .outro-row {
          padding: 0px 14px;
          display: flex;
          align-items: center;
          min-height: 22px;
          margin-bottom: 0px;
          justify-content: space-between;
        }

        #receipt .outro-row>span {
          font-size: 12px;
        }

        #receipt #foods {
          padding: 0 14px;
        }

        #receipt h4.category-name {
          margin-bottom: 5px;
        }

        #receipt .food {
          margin-bottom: 10px;
        }

        #receipt .food-row,
        #receipt .extra-row {
          display: flex;
          align-items: center;
        }

        #receipt span.food-quantity,
        #receipt span.extra-quantity {
          flex: 0 0 auto;
          width: 11%;
          /* background: red;*/
        }

        #receipt span.food-name,
        #receipt span.extra-name {
          flex: 0 0 auto;
          width: 65%;
          box-sizing: border-box;
          /*background:green;*/
          padding-left: 2px;
        }

        #receipt span.food-price,
        #receipt span.extra-price {
          flex: 0 0 auto;
          text-align: right;
          width: 24%;
          /*background: blue;*/
        }

        #receipt .food-row>span,
        #receipt .extra-row>span {
          font-size: 12px;
        }

        #receipt div.marker {
          border-top: 1px solid black;
          margin: 15px 14px;
        }

        #receipt #total {
          height: 10px;
        }
      </style>
    </div>

    <div id="receipt-body" style="display:none">
      <div id="receipt" style="visibility: visible;transform: scale(1);font-family: 'Poppins', sans-serif;width: 58mm;padding-bottom: 50px;line-height: normal;color: black;position: absolute;left: 0;top: 0;z-index: -100000000;display: block !important;-webkit-print-color-adjust: exact !important;">

          <img src="https://gurukitchen.foodonlinedelivery.co.uk/storage/app/public/logos/logo.png" alt="brand-logo" id="logo" style="visibility: visible;width: 50mm;height: 50mm;display: block;margin: 0px auto;">

          <h2 id="order-id" style="visibility: visible;text-align: center;margin-top: 0px;margin-bottom: 15px;">#528</h2>

          <div id="customer-name" class="intro-row" style="visibility: visible;padding: 0px 14px;display: flex;align-items: center;min-height: 27px;margin-bottom: 5px;">
              <i class="fal fa-user" style="visibility: visible;-moz-osx-font-smoothing: grayscale;-webkit-font-smoothing: antialiased;display: inline-block;font-style: normal;font-variant: normal;text-rendering: auto;line-height: 1;font-weight: 300;font-family: &quot;Font Awesome 5 Pro&quot;;width: 16px;height: 16px;flex-grow: 0;flex-shrink: 0;"></i>
              <span style="visibility: visible;font-size: 12px;text-align: left;margin-left: 10px;">John Doe</span>
          </div>

          <div id="customer-phone" class="intro-row" style="visibility: visible;padding: 0px 14px;display: flex;align-items: center;min-height: 27px;margin-bottom: 5px;">
              <i class="fal fa-phone" style="visibility: visible;-moz-osx-font-smoothing: grayscale;-webkit-font-smoothing: antialiased;display: inline-block;font-style: normal;font-variant: normal;text-rendering: auto;line-height: 1;font-weight: 300;font-family: &quot;Font Awesome 5 Pro&quot;;width: 16px;height: 16px;flex-grow: 0;flex-shrink: 0;"></i>
              <span style="visibility: visible;font-size: 12px;text-align: left;margin-left: 10px;">9051134662</span>
          </div>

          <div id="delivery-address" class="intro-row" style="visibility: visible;padding: 0px 14px;display: flex;align-items: center;min-height: 27px;margin-bottom: 5px;">
              <i class="fal fa-map-marker-alt" style="visibility: visible;-moz-osx-font-smoothing: grayscale;-webkit-font-smoothing: antialiased;display: inline-block;font-style: normal;font-variant: normal;text-rendering: auto;line-height: 1;font-weight: 300;font-family: &quot;Font Awesome 5 Pro&quot;;width: 16px;height: 16px;flex-grow: 0;flex-shrink: 0;"></i>
              <span style="visibility: visible;font-size: 12px;text-align: left;margin-left: 10px;">341 Warwick Rd, Carlisle CA1 2BS, UK</span>
          </div>

          <div id="delivery-address" class="intro-row" style="visibility: visible;padding: 0px 14px;display: flex;align-items: center;min-height: 27px;margin-bottom: 5px;">
              <i class="fal fa-truck-couch" style="visibility: visible;-moz-osx-font-smoothing: grayscale;-webkit-font-smoothing: antialiased;display: inline-block;font-style: normal;font-variant: normal;text-rendering: auto;line-height: 1;font-weight: 300;font-family: &quot;Font Awesome 5 Pro&quot;;width: 16px;height: 16px;flex-grow: 0;flex-shrink: 0;"></i>
              <span style="visibility: visible;font-size: 12px;text-align: left;margin-left: 10px;">Delivery</span>
          </div>


          <div id="delivery-address" class="intro-row" style="visibility: visible;padding: 0px 14px;display: flex;align-items: center;min-height: 27px;margin-bottom: 5px;">
              <i class="fal fa-credit-card" style="visibility: visible;-moz-osx-font-smoothing: grayscale;-webkit-font-smoothing: antialiased;display: inline-block;font-style: normal;font-variant: normal;text-rendering: auto;line-height: 1;font-weight: 300;font-family: &quot;Font Awesome 5 Pro&quot;;width: 16px;height: 16px;flex-grow: 0;flex-shrink: 0;"></i>
              <span style="visibility: visible;font-size: 12px;text-align: left;margin-left: 10px;">Visa 4242</span>
          </div>

          <div id="restaurant-name" class="intro-row" style="visibility: visible;padding: 0px 14px;display: flex;align-items: center;min-height: 27px;margin-bottom: 5px;">
              <i class="fal fa-utensils" style="visibility: visible;-moz-osx-font-smoothing: grayscale;-webkit-font-smoothing: antialiased;display: inline-block;font-style: normal;font-variant: normal;text-rendering: auto;line-height: 1;font-weight: 300;font-family: &quot;Font Awesome 5 Pro&quot;;width: 16px;height: 16px;flex-grow: 0;flex-shrink: 0;"></i>
              <span style="visibility: visible;font-size: 12px;text-align: left;margin-left: 10px;">Quarter Lounge</span>
          </div>




          <div class="marker" style="visibility: visible;border-top: 1px solid black;margin: 15px 14px;"></div>

          <div id="foods" style="visibility: visible;padding: 0 14px;">

              <div class="food-category" style="visibility: visible;">
                  <h4 class="category-name" style="visibility: visible;margin-bottom: 5px;">Starters</h4>

                  <div class="food" style="visibility: visible;margin-bottom: 10px;">
                      <div class="food-row" style="visibility: visible;display: flex;align-items: center;">
                          <span class="food-quantity" style="visibility: visible;flex: 0 0 auto;width: 11%;font-size: 12px;">5x</span>
                          <span class="food-name" style="visibility: visible;flex: 0 0 auto;width: 65%;box-sizing: border-box;padding-left: 2px;font-size: 12px;">Chipotle Chicken Strips</span>
                          <span class="food-price" style="visibility: visible;flex: 0 0 auto;text-align: right;width: 24%;font-size: 12px;">£6.95</span>
                      </div>


                  </div>

              </div>
              <div class="food-category" style="visibility: visible;">
                  <h4 class="category-name" style="visibility: visible;margin-bottom: 5px;">Chicken and Wings</h4>

                  <div class="food" style="visibility: visible;margin-bottom: 10px;">
                      <div class="food-row" style="visibility: visible;display: flex;align-items: center;">
                          <span class="food-quantity" style="visibility: visible;flex: 0 0 auto;width: 11%;font-size: 12px;">2x</span>
                          <span class="food-name" style="visibility: visible;flex: 0 0 auto;width: 65%;box-sizing: border-box;padding-left: 2px;font-size: 12px;">Sharing wings x 12 wings</span>
                          <span class="food-price" style="visibility: visible;flex: 0 0 auto;text-align: right;width: 24%;font-size: 12px;">£15.00</span>
                      </div>


                  </div>

              </div>

          </div>

          <div class="marker" style="visibility: visible;border-top: 1px solid black;margin: 15px 14px;"></div>

          <div id="subtotal" class="outro-row" style="visibility: visible;padding: 0px 14px;display: flex;align-items: center;min-height: 22px;margin-bottom: 0px;justify-content: space-between;">
              <span style="visibility: visible;font-size: 12px;">Subtotal</span>
              <span style="visibility: visible;font-size: 12px;">£64.75</span>
          </div>

          <div id="delivery-fee" class="outro-row" style="visibility: visible;padding: 0px 14px;display: flex;align-items: center;min-height: 22px;margin-bottom: 0px;justify-content: space-between;">
              <span style="visibility: visible;font-size: 12px;">Delivery Fee</span>
              <span style="visibility: visible;font-size: 12px;">£25.00</span>
          </div>

          <div class="marker" style="visibility: visible;border-top: 1px solid black;margin: 15px 14px;"></div>

          <div id="total" class="outro-row" style="visibility: visible;padding: 0px 14px;display: flex;align-items: center;min-height: 22px;margin-bottom: 0px;justify-content: space-between;height: 10px;">
              <h4 style="visibility: visible;">Total</h4>
              <h4 style="visibility: visible;">£89.75</h4>
          </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('receipt')
@include('orders.receipt')
@endsection

@push('scripts')
<script type="text/javascript">
  $(window).on('load', () => {
    let target = document.getElementById("printOrderWithStar");
    let receiptHeadHtml = document.getElementById('receipt-head').innerHTML;
    let receiptBodyHtml = document.getElementById('receipt-body').innerHTML;
    //let receiptBodyHtml = document.getElementById('receipt').outerHTML;

    let passprnt_uri = "starpassprnt://v1/print/nopreview?";
    //let receipt_html = `<html><head>${receiptHeadHtml}</head><body>${receiptBodyHtml}</body></html>`;
    let receipt_html = `<html><head></head><body>${receiptBodyHtml}</body></html>`;

    console.log(receipt_html);

    passprnt_uri = passprnt_uri + "back=" + encodeURIComponent(window.location.href);
    passprnt_uri = passprnt_uri + "&html=" + encodeURIComponent(receipt_html);

    target.href = passprnt_uri;

  });

  $("#printOrder").on("click", () => window.print());
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/all.css') }}">
<link rel="stylesheet" href="{{ asset('css/receipt.css') }}">
@endpush