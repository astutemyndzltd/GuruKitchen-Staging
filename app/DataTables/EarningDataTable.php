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
            ->editColumn('total_delivered', function ($result) {
                return $result->total_delivered;
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
                $driverCommission = round($result->driver_commission, 2);
                $taxTotal = round(($taxRate / 100) * ($commission + $driverCommission), 2);
                $driverFees = $result->driver_fee;
                $net = $result->gross - ($commission + $taxTotal + $driverCommission + $driverFees);
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
                'data' => 'total_delivered',
                'title' => 'Delivered',
                'orderable' => false,
                'searchable' => false

            ],
            [
                'data' => 'gross',
                'title' => 'Gross Revenue',
                'orderable' => false,
                'searchable' => false

            ],
            /*[
                'data' => 'commission_tax',
                'title' => 'Commission / Tax',
                'orderable' => false,
                'searchable' => false

            ],*/
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
            $driverCommission = setting('driver_commission', 0);
            
            $statement = "select coalesce(d2.total, 0) total, coalesce(d2.total_delivered, 0) total_delivered,
            coalesce(d2.gross, 0) gross, driver_fee, driver_commission, r.id rest_id, r.name rest_name, 
            r.admin_commission commission, date(d2.mindate) startdate, date(d2.maxdate) enddate
            from (select count(*) total, count(case when order_status_id = 5 then 1 else null end) total_delivered,
            sum(if(order_status_id = 5, price, 0)) gross, sum(if(order_status_id = 5, app_del_fee, 0)) driver_fee, 
            sum(if(order_status_id = 5, driver_com, 0)) driver_commission, res_id, min(created_at) mindate, max(created_at) maxdate 
            from (select o.id id, p.price, if(o.use_app_drivers = 1, o.delivery_fee, 0) app_del_fee,
            (if(o.use_app_drivers = 1, $driverCommission, 0) / 100 * (p.price - o.delivery_fee)) driver_com, 
            o.delivery_fee, ro.res_id, o.created_at, o.order_status_id from orders o
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