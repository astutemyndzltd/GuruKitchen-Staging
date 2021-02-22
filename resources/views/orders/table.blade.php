@push('css_lib')
@include('layouts.datatables_css')
@endpush

{!! $dataTable->table(['width' => '100%']) !!}

@push('scripts_lib')
@include('layouts.datatables_js')
{!! $dataTable->scripts() !!}

<script>
 $(window).on('load', () => {

    const table = $('#dataTableBuilder').DataTable({
            "fnDrawCallback": function(oSettings) {
                $('<span>anik</span>').prependTo($('#dataTableBuilder_wrapper'));
            }
    });
    

    $('#dataTableBuilder thead tr').clone(true).appendTo('#dataTableBuilder thead');

    /*$('#dataTableBuilder thead tr:eq(1) th').each(function (i) {

        let title = $(this).text();

        if (title == 'Actions') {
            $(this).html('');
        }

        $(this).attr('class', 'search-cell-header');

        $(this).html( '<input type="text" class="search-cell" placeholder="Search" />' );
 
        $('input', this).on('keyup change', function () {
            if (table.column(i).search() !== this.value) {
                table.column(i).search(this.value).draw();
            }
        });

    });*/

    //let dataTable = $('#dataTableBuilder').DataTable();
    //console.log(dataTable);


 });
</script>
@endpush