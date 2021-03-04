<?php

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Earning;
use App\Models\Order;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class EarningDataTable extends DataTable
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
            ->editColumn('rest_name', function ($result) {
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
                $commission = ($comRate / 100) * $result->gross;
                $taxTotal = ($taxRate / 100) * $commission;
                return getPrice($commission) . " ($comRate%) / " . getPrice($taxTotal) . " ($taxRate%)";
            })
            ->editColumn('earning', function ($result) {
                $taxRate = setting('default_tax', 0);
                $comRate = $result->commission;
                $gross = $result->gross;
                $net = $gross - (($gross * $comRate * ($taxRate + 100)) / 10000);
                return getPrice($net);
            })
            ->rawColumns($columns);

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
                'data' => 'rest_name',
                'title' => 'Restaurant',
                'orderable' => false

            ],
            [
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
            ]
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
            $statement = "select coalesce(d2.total, 0) total, coalesce(d2.gross, 0) gross, r.id rest_id, r.name rest_name, r.admin_commission commission 
            from (select count(*) total, sum(price) gross, res_id from (select o.id id, p.price, ro.res_id from orders o
            join payments p on o.payment_id = p. id and o.paid_out = 0 and o.active = 1
            join (select fo.order_id, f.restaurant_id res_id from food_orders fo join foods f on fo.food_id = f.id group by fo.order_id) ro
            on o.id = ro.order_id) d group by res_id) d2 right join restaurants r on r.id = d2.res_id";

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
            ->addAction(['title'=>trans('lang.actions'),'width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
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