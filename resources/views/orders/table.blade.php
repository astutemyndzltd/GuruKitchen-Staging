@push('css_lib')
@include('layouts.datatables_css')
@endpush

{!! $dataTable->table(['width' => '100%']) !!}

@push('scripts_lib')
@include('layouts.datatables_js')
{!! $dataTable->scripts() !!}

<script>
 $(window).on('load', () => {

    console.log('my name is anik banerjee');

    const table = $('#dataTableBuilder').DataTable();

    let $divForCheckbox = $('<div class="col-lg-4 col-xs-12"></div>');
    $divForCheckbox.insertBefore('#dataTableBuilder_wrapper div.ml-auto');


    let html = `<div class="checkbox icheck">
                <label class="col-9 ml-2 form-check-inline">
                    <input name="available_for_delivery" type="hidden" value="0" id="available_for_delivery">
                    <input checked="checked" name="available_for_delivery" type="checkbox" value="1" id="available_for_delivery">
                </label>
            </div>`;


    const $checkbox = $(html);
    $checkbox.appendTo($divForCheckbox);
    $checkbox.iCheck();

 });
</script>
@endpush