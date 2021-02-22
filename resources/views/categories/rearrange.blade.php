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
<link rel="stylesheet" href="{{asset('plugins/jQueryUI/jquery-ui.min.css')}}">
<link rel="stylesheet" href="{{asset('css/rearrange.css')}}">
@endpush

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{trans('lang.category_plural')}}<small class="ml-3 mr-3">|</small><small>{{trans('lang.category_desc')}}</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
          <li class="breadcrumb-item"><a href="{!! route('categories.index') !!}">{{trans('lang.category_plural')}}</a>
          </li>
          <li class="breadcrumb-item active">{{trans('lang.category_create')}}</li>
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
        @can('categories.index')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('categories.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.category_table')}}</a>
        </li>
        @endcan
        <li class="nav-item">
          <a class="nav-link" href="{!! route('categories.create') !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.category_create')}}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="/categories/rearrange"><i class="fa fa-sort-numeric-asc mr-2"></i>Rearrange Categories</a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      <form action="/categories/store-rearranged" method="post">
        @csrf
        <div class="row">
          <ul id="categories" style="height:{{  (ceil(count($categories) / 4) * 52) + 5   }}px;">
            @foreach($categories as $category)
            <li id="{{ $category->id }}" title="{{ $category->name }}">
              
              @if($category->hasMedia('image'))
                <img src="{{ $category->getFirstMediaUrl('image', 'icon') }}" alt="category-image">
              @else
                <img src="{{ asset('images/image_default.png') }}" alt="category-image">
              @endif

              <span>{{ $category->name }}</span>
            </li>
            @endforeach
          </ul>
        </div>
        <div class="form-group col-12 text-right">
          <input type="hidden" name="ordering" id="ordering">
          <span class="tips"><i>Drag and drop to rearrange items in any desired order</i></span>
          <button type="submit" class="btn btn-{{setting('theme_color')}}" id="btnSaveArrangement"><i class="fa fa-save"></i> {{trans('lang.save')}} Arrangement</button>
          <a href="{!! route('categories.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
        </div>
      </form>
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
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/jQueryUI/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
    Dropzone.autoDiscover = false;
    var dropzoneFields = [];
</script>
<script type="text/javascript">
  $(window).on('load', function() {
    $('#categories').sortable();
    $('#btnSaveArrangement').on('click', () => {
      var ordering = $('#categories').sortable('toArray');
      $('#ordering').val(JSON.stringify(ordering));
    });
  });
</script>
@endpush