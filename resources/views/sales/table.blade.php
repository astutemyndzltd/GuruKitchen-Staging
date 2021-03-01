@push('css_lib')
@include('layouts.datatables_css')
@endpush

{!! $dataTable->table(['width' => '100%']) !!}

@push('scripts_lib')
@include('layouts.datatables_js')
<script>
    let start, end;
    let divStatistics, divTotalOrders, divGrossRevenue, divAvgOrderValue;

    function onReloadDt(data) {
        if (start && end) {
            data.startDate = start.format('YYYY-MM-DD');
            data.endDate = end.format('YYYY-MM-DD');
        }
    }

    function onDataReceived(json) {
        $(divStatistics).slideDown();
        $(divTotalOrders).text('25');
        $(divGrossRevenue).text('£768.25');
        $(divAvgOrderValue).text('£25.68');
    }

</script>
{!! $dataTable->scripts() !!}
<script>
    $(window).on('load', () => {

        divStatistics = document.querySelector('div.statistics');
        divTotalOrders = divStatistics.querySelector('div.total-orders');
        divGrossRevenue = divStatistics.querySelector('div.gross-revenue');
        divAvgOrderValue = divStatistics.querySelector('div.avg-order-val');

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

        onDateRangeChange(moment(), moment());
    });
</script>
@endpush