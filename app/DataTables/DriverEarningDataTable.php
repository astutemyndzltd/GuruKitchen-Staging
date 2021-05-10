<?php

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Earning;
use App\Models\Order;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class DriverEarningDataTable extends DataTable
{
    /**
     * custom fields columns
     * @var array
     */
    public static $customFields = [];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($collection)
    {        
        $dataTable = new CollectionDataTable(collect($collection));
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('delivery_fee', function ($result) {
                return getPriceColumn($result, 'delivery_fee');
            });
            /*->editColumn('rest_name', function ($result) {
                return $result->rest_name;
            })
            ->editColumn('total', function ($result) {
                return $result->total;
            })
            ->editColumn('gross', function ($result) {
                return getPrice($result->gross);
            })
            ->editColumn('commission_tax', function ($result) {
                $taxRate = setting('default_tax', 0);
                $comRate = $result->commission;
                $commission = round(($comRate / 100) * $result->gross, 2);
                $taxTotal = round(($taxRate / 100) * $commission, 2);
                return getPrice($commission) . " / " . getPrice($taxTotal);
            })
            ->editColumn('earning', function ($result) {
                $taxRate = setting('default_tax', 0);
                $comRate = $result->commission;
                $commission = round(($comRate / 100) * $result->gross, 2);
                $taxTotal = round(($taxRate / 100) * $commission, 2);
                $net = $result->gross - ($commission + $taxTotal);
                return getPrice($net);
            })
            ->editColumn('period', function ($result) {
                if (isset($result->startdate) && isset($result->enddate)) {
                    return date('d M Y', strtotime($result->startdate)) . ' - ' . date('d M Y', strtotime($result->enddate)); 
                }
                else {
                    return 'NA';
                }       
            })
            ->rawColumns($columns);*/

        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            [
                'data' => 'name',
                'title' => 'Driver',
                'orderable' => false

            ],
            [
                'data' => 'orders',
                'title' => 'Orders',
                'orderable' => false,
                'searchable' => false
            ],
            [
                'data' => 'delivery_fee',
                'title' => 'Delivery Fee',
                'orderable' => false,
                'searchable' => false
            ]
            /*[
                'data' => 'total',
                'title' => 'Orders',
                'orderable' => false,
                'searchable' => false

            ],
            [
                'data' => 'gross',
                'title' => 'Gross Revenue',
                'orderable' => false,
                'searchable' => false

            ],
            [
                'data' => 'commission_tax',
                'title' => 'Commission / Tax',
                'orderable' => false,
                'searchable' => false

            ],
            [
                'data' => 'earning',
                'title' => 'Earning',
                'orderable' => false,
                'searchable' => false
            ],
            [
                'data' => 'period',
                'title' => 'Period',
                'orderable' => false,
                'searchable' => false
            ]*/
        ];

        return $columns;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        if (auth()->user()->hasRole('admin')) 
        {
            $statement = "select u.id, u.name, coalesce(ds.orders, 0) orders, 
            date(ds.from_date) from_date, date(ds.to_date) to_date,
            coalesce(ds.delivery_fee, 0) delivery_fee, coalesce(ds.total, 0) total
            from users u join drivers d on u.id = d.user_id
            left outer join (select o.driver_id, count(*) orders, min(o.created_at) from_date, max(o.created_at) to_date,
            sum(o.delivery_fee) delivery_fee, sum(p.price) total
            from orders o join payments p on o.payment_id = p.id
            where o.active = 1 and o.driver_id is not null 
            and o.order_status_id = 5 and o.driver_paid_out = 0
            group by o.driver_id) ds on d.user_id = ds.driver_id";

            $results = DB::select(DB::raw($statement));

            return $results;
        }
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters(array_merge(
                config('datatables-buttons.parameters'), [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ), true)
                ]
            ));
    }

    /**
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'earningsdatatable_' . time();
    }
}