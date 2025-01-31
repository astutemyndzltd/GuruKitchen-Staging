@if($customFields)
<h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Name Field -->
    <div class="form-group row ">
        {!! Form::label('name', trans("lang.restaurant_name"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('name', null, ['class' => 'form-control','placeholder'=> trans("lang.restaurant_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_name_help") }}
            </div>
        </div>
    </div>
    <!-- cuisines Field -->
    <div class="form-group row ">
        {!! Form::label('cuisines[]', trans("lang.restaurant_cuisines"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('cuisines[]', $cuisine, $cuisinesSelected, ['class' => 'select2 form-control' , 'multiple'=>'multiple']) !!}
            <div class="form-text text-muted">{{ trans("lang.restaurant_cuisines_help") }}</div>
        </div>
    </div>
    @hasanyrole('admin|manager')
    

    <!-- min_order_amount Field -->
    <div class="form-group row ">
        {!! Form::label('min_order_amount', 'Minimum Order Amount', ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('min_order_amount', isset($restaurant) ? $restaurant->min_order_amount : 0, ['class' => 'form-control','step'=>'any','placeholder'=> 'Edit the minimum order amount']) !!}
            <div class="form-text text-muted">
                Enter the minimum order amount for this restaurant
            </div>
        </div>
    </div>

    <!-- delivery_fee Field -->
    <div class="form-group row ">
        {!! Form::label('delivery_fee', trans("lang.restaurant_delivery_fee"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('delivery_fee', null, ['class' => 'form-control','step'=>'any','placeholder'=> trans("lang.restaurant_delivery_fee_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_delivery_fee_help") }}
            </div>
        </div>
    </div>

    <!-- delivery_range Field -->
    <div class="form-group row ">
        {!! Form::label('delivery_range', trans("lang.restaurant_delivery_range"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('delivery_range', null, ['class' => 'form-control', 'step'=>'any','placeholder'=> trans("lang.restaurant_delivery_range_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_delivery_range_help") . " in km" }}
            </div>
        </div>
    </div>

    <!-- default_tax Field -->
    {{-- <div class="form-group row ">
        {!! Form::label('default_tax', trans("lang.restaurant_default_tax"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('default_tax', null, ['class' => 'form-control', 'step'=>'any','placeholder'=> trans("lang.restaurant_default_tax_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_default_tax_help") }}
            </div>
        </div>
    </div> --}}

    <input type="hidden" name="default_tax" value="0">

    @endhasanyrole

    <!-- Phone Field -->
    <div class="form-group row ">
        {!! Form::label('phone', trans("lang.restaurant_phone"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('phone', null, ['class' => 'form-control','placeholder'=> trans("lang.restaurant_phone_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_phone_help") }}
            </div>
        </div>
    </div>

    <!-- Mobile Field -->
    <div class="form-group row ">
        {!! Form::label('mobile', trans("lang.restaurant_mobile"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('mobile', null, ['class' => 'form-control','placeholder'=> trans("lang.restaurant_mobile_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_mobile_help") }}
            </div>
        </div>
    </div>

    <!-- Address Field -->
    <div class="form-group row ">
        {!! Form::label('address', trans("lang.restaurant_address"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('address', null, ['class' => 'form-control','placeholder'=> trans("lang.restaurant_address_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_address_help") }}
            </div>
        </div>
    </div>

    <!-- Latitude Field -->
    <div class="form-group row ">
        {!! Form::label('latitude', trans("lang.restaurant_latitude"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('latitude', null, ['class' => 'form-control','placeholder'=> trans("lang.restaurant_latitude_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_latitude_help") }}
            </div>
        </div>
    </div>

    <!-- Longitude Field -->
    <div class="form-group row ">
        {!! Form::label('longitude', trans("lang.restaurant_longitude"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('longitude', null, ['class' => 'form-control','placeholder'=> trans("lang.restaurant_longitude_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.restaurant_longitude_help") }}
            </div>
        </div>
    </div>
    <!-- 'Boolean closed Field' -->
    <div class="form-group row ">
        {!! Form::label('closed', trans("lang.restaurant_closed"),['class' => 'col-3 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('closed', 0) !!}
                {!! Form::checkbox('closed', 1, null) !!}
            </label>
        </div>
    </div>

    
</div>

<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

    <!-- Image Field -->
    <div class="form-group row">
        {!! Form::label('image', trans("lang.restaurant_image"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            <div style="width: 100%" class="dropzone image" id="image" data-field="image">
                <input type="hidden" name="image">
            </div>
            <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal" class="btn btn-outline-{{setting('theme_color','primary')}} btn-sm float-right mt-1">{{ trans('lang.media_select')}}</a>
            <div class="form-text text-muted w-50">
                {{ trans("lang.restaurant_image_help") }}
            </div>
        </div>
    </div>

    @prepend('scripts')
        <script type="text/javascript">
            var var15671147011688676454ble = '';
            @if(isset($restaurant) && $restaurant->hasMedia('image'))
                var15671147011688676454ble = {
                name: "{!! $restaurant->getFirstMedia('image')->name !!}",
                size: "{!! $restaurant->getFirstMedia('image')->size !!}",
                type: "{!! $restaurant->getFirstMedia('image')->mime_type !!}",
                collection_name: "{!! $restaurant->getFirstMedia('image')->collection_name !!}"
            };
                    @endif
            var dz_var15671147011688676454ble = $(".dropzone.image").dropzone({
                    url: "{!!url('uploads/store')!!}",
                    addRemoveLinks: true,
                    maxFiles: 1,
                    init: function () {
                        @if(isset($restaurant) && $restaurant->hasMedia('image'))
                        dzInit(this, var15671147011688676454ble, '{!! url($restaurant->getFirstMediaUrl('image','thumb')) !!}')
                        @endif
                    },
                    accept: function (file, done) {
                        dzAccept(file, done, this.element, "{!!config('medialibrary.icons_folder')!!}");
                    },
                    sending: function (file, xhr, formData) {
                        dzSending(this, file, formData, '{!! csrf_token() !!}');
                    },
                    maxfilesexceeded: function (file) {
                        dz_var15671147011688676454ble[0].mockFile = '';
                        dzMaxfile(this, file);
                    },
                    complete: function (file) {
                        dzComplete(this, file, var15671147011688676454ble, dz_var15671147011688676454ble[0].mockFile);
                        dz_var15671147011688676454ble[0].mockFile = file;
                    },
                    removedfile: function (file) {
                        dzRemoveFile(
                            file, var15671147011688676454ble, '{!! url("restaurants/remove-media") !!}',
                            'image', '{!! isset($restaurant) ? $restaurant->id : 0 !!}', '{!! url("uplaods/clear") !!}', '{!! csrf_token() !!}'
                        );
                    }
                });
            dz_var15671147011688676454ble[0].mockFile = var15671147011688676454ble;
            dropzoneFields['image'] = dz_var15671147011688676454ble;
        </script>
@endprepend

    <!-- Description Field -->
    <div class="form-group row ">
        {!! Form::label('description', trans("lang.restaurant_description"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::textarea('description', null, ['class' => 'form-control','placeholder'=>
            trans("lang.restaurant_description_placeholder") ]) !!}
            <div class="form-text text-muted">{{ trans("lang.restaurant_description_help") }}</div>
        </div>
    </div>
    <!-- Information Field -->
    <div class="form-group row ">
        {!! Form::label('information', trans("lang.restaurant_information"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::textarea('information', null, ['class' => 'form-control','placeholder'=>
            trans("lang.restaurant_information_placeholder") ]) !!}
            <div class="form-text text-muted">{{ trans("lang.restaurant_information_help") }}</div>
        </div>
    </div>

</div>



<div class="form-group row ">

    <div class="available_for">
        {!! Form::label('available_for_delivery', trans("lang.restaurant_available_for_delivery"),['class' => 'col-7 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('available_for_delivery', 0) !!}
                {!! Form::checkbox('available_for_delivery', 1, null) !!}
            </label>
        </div>
    </div>

    <div class="available_for">
        {!! Form::label('available_for_pickup', 'Available for pickup', ['class' => 'col-7 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('available_for_pickup', 0) !!}
                {!! Form::checkbox('available_for_pickup', 1, null) !!}
            </label>
        </div>
    </div>

    <div class="available_for">
        {!! Form::label('available_for_preorder', 'Available for preorder', ['class' => 'col-7 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('available_for_preorder', 0) !!}
                {!! Form::checkbox('available_for_preorder', 1, null) !!}
            </label>
        </div>
    </div>
</div>



<!-- pre-order -->
<div class="col-12 custom-field-container preorder-main">

    <input type="hidden" name="opening_times" id="opening_times">

    <?php $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']; ?>

    <h5 class="col-12 pb-4">Set Your Opening Hours</h5>
    <p class="col-12">Select each day you're open</p>

    <?php $oldOpeningTimes = old('opening_times') != null ? json_decode(old('opening_times'), true) : null; ?>


    <input type="hidden" value="{{ json_encode($oldOpeningTimes) }}" > 

    <div class="preorder-container">
        @foreach ($weekdays as $day)
        <div class="weekday form-group row">
            
            <div class="checkbox icheck">
                <label class="col-9 ml-2 form-check-inline">
                    <?php
                        $resInactive = !isset($restaurant) || $restaurant->opening_times == null || $restaurant->opening_times[$day] == null;
                        $oldOpenTimesInactive = $oldOpeningTimes == null || $oldOpeningTimes[$day] == null;
                        $dayChecked = !($resInactive && $oldOpenTimesInactive);
                    ?>              
                    {!! Form::checkbox($day, 1, $dayChecked, [ 'id' => $day]) !!}
                </label>
            </div>

            {!! Form::label($day, ucfirst($day), ['class' => 'col-2 control-label']) !!}

            <div class="timings">
                @if($resInactive && $oldOpenTimesInactive)
                    <span>Closed all day</span>
                @else
                    <?php $spans = isset($oldOpeningTimes) ? $oldOpeningTimes[$day] : $restaurant->opening_times[$day]; ?>
                    @foreach($spans as $timeSpan)
                        <div class="timing">
                            <input type="text" readonly class="start" placeholder="Start time" value="{!! $timeSpan['opens_at'] !!}">
                            <input type="text" readonly class="end" placeholder="End time" value="{!! $timeSpan['closes_at'] !!}">
                            <button type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </div>
                    @endforeach
                @endif
            </div>

            <a class="add-hrs">+ Add Hours</a>

        </div>
        @endforeach
    </div>
</div>


@hasrole('admin')
<div class="col-12 custom-field-container">
    <h5 class="col-12 pb-4">{!! trans('lang.admin_area') !!}</h5>

    <div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
        <!-- Users Field -->
        <div class="form-group row ">
            {!! Form::label('users[]', trans("lang.restaurant_users"),['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
                {!! Form::select('users[]', $user, $usersSelected, ['class' => 'select2 form-control' , 'multiple'=>'multiple']) !!}
                <div class="form-text text-muted">{{ trans("lang.restaurant_users_help") }}</div>
            </div>
        </div>

        <!-- Drivers Field -->
        <div class="form-group row ">
            {!! Form::label('drivers[]', 'Delivery Drivers',['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
                {!! Form::select('drivers[]', $drivers, $driversSelected, ['class' => 'select2 form-control' , 'multiple'=>'multiple']) !!}
                <div class="form-text text-muted">Select delivery drivers for this restaurant</div>
            </div>
        </div>

    </div>


    <div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
        <!-- admin_commission Field -->
        <div class="form-group row ">
            {!! Form::label('admin_commission', trans("lang.restaurant_admin_commission"), ['class' => 'col-3 control-label text-right']) !!}
            <div class="col-9">
                {!! Form::number('admin_commission', null, ['class' => 'form-control', 'step'=>'any', 'placeholder'=> trans("lang.restaurant_admin_commission_placeholder")]) !!}
                <div class="form-text text-muted">
                    {{ trans("lang.restaurant_admin_commission_help") }}
                </div>
            </div>
        </div>

        <div class="form-group row" style="padding-top:14px;">

            <div class="form-group row col-6">  
                {!! Form::label('active', trans("lang.restaurant_active"), ['class' => 'col-8 control-label text-right']) !!}
                <div class="checkbox icheck">
                    <label class="col-9 ml-2 form-check-inline">
                        {!! Form::hidden('active', 0) !!}
                        {!! Form::checkbox('active', 1, null) !!}
                    </label>
                </div>
            </div>

            <div class="form-group row col-6">
                {!! Form::label('use_app_drivers', 'Enable GK Drivers', ['class' => 'col-8 control-label text-right']) !!}
                <div class="checkbox icheck">
                    <label class="col-9 ml-2 form-check-inline">
                        {!! Form::hidden('use_app_drivers', 0) !!}
                        {!! Form::checkbox('use_app_drivers', 1, null) !!}
                    </label>
                </div>
            </div>

        </div>

    </div>
</div>
@endhasrole

@if($customFields)
<div class="clearfix"></div>
<div class="col-12 custom-field-container">
    <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
    {!! $customFields !!}
</div>
@endif


<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button id="save-restaurant" type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.restaurant')}}</button>
    <a href="{!! route('restaurants.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>