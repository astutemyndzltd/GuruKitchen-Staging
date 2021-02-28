@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1 class="m-0 text-dark"> Sales<small class="ml-3 mr-3"></small></h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('sales.index') !!}">Sales</a></li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="content">
    <div class="clearfix"></div>
    @include('flash::message')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                @include('layouts.right_toolbar', compact('dataTable'))
            </ul>
        </div>
        <div class="card-body">
            <div class="daterange-choose">
                <span>Select Date Range</span>  
                <input type="text" name="daterange" id="daterangepicker" readonly/>
            </div>
            <div class="statistics">
                <div class="chunk">
                    <div class="capt">Total Orders</div>
                    <div class="sub">25</div>
                </div>
                <div class="chunk">
                    <div class="capt">Gross Revenue</div>
                    <div class="sub">£768.25</div>
                </div>
                <div class="chunk">
                    <div class="capt">Average Order Value</div>
                    <div class="sub">£25.68</div>
                </div>
            </div>
            @include('sales.table')
            <div class="clearfix"></div>
        </div>
    </div>
</div>

@endsection

@push('css_lib')
<link rel="stylesheet" href="{{ asset('css/sales-datatable.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
@endpush

@push('scripts_lib')
<script type="text/javascript" src="{{ asset('plugins/daterangepicker/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
@endpush