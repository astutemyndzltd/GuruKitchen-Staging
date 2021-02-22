@push('css_lib')
@include('layouts.datatables_css')
@endpush

{!! $dataTable->table(['width' => '100%']) !!}

@push('scripts_lib')
@include('layouts.datatables_js')
{!! $dataTable->scripts() !!}
<script>
 $(window).on('load', () => {
   let dataTable = $('#dataTableBuilder').DataTable();
   console.log(dataTable);
 });
</script>
@endpush