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
        <div class="card-header"></div>
    </div>
</div>

@endsection