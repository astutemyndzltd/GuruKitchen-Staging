@extends('layouts.app')

@push('css_lib')
<!-- iCheck -->
<link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">
<!-- select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">
{{--dropzone--}}
<link rel="stylesheet" href="{{asset('plugins/dropzone/bootstrap.min.css')}}">
{{--timepicker--}}
<link rel="stylesheet" href="{{asset('plugins/timepicker-jquery/mdtimepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker-jquery/mdtimepicker-theme.css')}}">
{{--preorder--}}
<link rel="stylesheet" href="{{asset('css/preorder.css')}}">
@endpush

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{trans('lang.restaurant_plural')}}<small class="ml-3 mr-3">|</small><small>{{trans('lang.restaurant_desc')}}</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
          <li class="breadcrumb-item"><a href="{!! route('restaurants.index') !!}">{{trans('lang.restaurant_plural')}}</a>
          </li>
          <li class="breadcrumb-item active">{{trans('lang.restaurant_edit')}}</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div class="content">
  <div class="clearfix"></div>
  @include('flash::message')
  @include('adminlte-templates::common.errors')
  <div class="clearfix"></div>
  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        @can('restaurants.index')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('restaurants.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.restaurant_table')}}</a>
        </li>
        @endcan
        @can('restaurants.create')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('restaurants.create') !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.restaurant_create')}}</a>
        </li>
        @endcan
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-pencil mr-2"></i>{{trans('lang.restaurant_edit')}}</a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      {!! Form::model($restaurant, ['route' => ['restaurants.update', $restaurant->id], 'method' => 'patch']) !!}
      <div class="row">
        @include('restaurants.fields')
      </div>
      {!! Form::close() !!}
      <div class="clearfix"></div>
    </div>
  </div>
</div>
@include('layouts.media_modal')
@endsection
@push('scripts_lib')
<!-- iCheck -->
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<!-- select2 -->
<script src="{{asset('plugins/select2/select2.min.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
{{--dropzone--}}
<script src="{{asset('plugins/dropzone/dropzone.js')}}"></script>
<script type="text/javascript">
    Dropzone.autoDiscover = false;
    let dropzoneFields = [];
</script>
{{--timepicker--}}
<script src="{{ asset('plugins/timepicker-jquery/mdtimepicker.min.js') }}"></script>
{{--preorder--}}
<script src="{{ asset('js/preorder.js') }}"></script>
@endpush