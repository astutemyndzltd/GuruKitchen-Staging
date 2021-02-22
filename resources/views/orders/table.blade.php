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
    $divForCheckbox.insertAfter('#dataTableBuilder_wrapper>div.row>div');

    const $checkbox = $('<input type="checkbox">');
    $checkbox.appendTo($divForCheckbox);

    //$($checkbox).iCheck();

 });
</script>
@endpush