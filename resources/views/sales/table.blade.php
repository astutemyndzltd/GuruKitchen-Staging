@push('css_lib')
@include('layouts.datatables_css')
@endpush

{!! $dataTable->table(['width' => '100%']) !!}

@push('scripts_lib')
@include('layouts.datatables_js')
<script>
    let start = moment();
    let end = moment();

    function onReloadDt(data) {
        //console.log(start.)
    }

</script>
{!! $dataTable->scripts() !!}
<script>
    $(window).on('load', () => {

        const $table = $('#dataTableBuilder').DataTable();

        $('#daterangepicker').daterangepicker({
            startDate: start,
            endDate: end,
            locale: { format: 'DD MMM YYYY' },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, onDateRangeChange);

        function onDateRangeChange(s, e) {
            start = s; end = e;
            $table.ajax.reload();
        }

        onDateRangeChange(start, end);
    });
</script>
@endpush