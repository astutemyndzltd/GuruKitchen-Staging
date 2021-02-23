@push('css_lib')
@include('layouts.datatables_css')
@endpush

{!! $dataTable->table(['width' => '100%']) !!}

@push('scripts_lib')
@include('layouts.datatables_js')
{!! $dataTable->scripts() !!}

<script>
 $(window).on('load', () => {

    const table = $('#dataTableBuilder').DataTable();

    let $divForCheckbox = $('<div class="col-lg-4 col-xs-12"></div>');
    $divForCheckbox.insertBefore('#dataTableBuilder_wrapper div.ml-auto');

    let html = `<label><input type="checkbox">Show Live Orders</label>`;
    const $checkbox = $(html).appendTo($divForCheckbox);
    $checkbox.iCheck({ checkboxClass: 'icheckbox_flat-blue' });

 });
</script>
@endpush