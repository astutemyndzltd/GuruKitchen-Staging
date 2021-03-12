<?php

namespace App\DataTables;

use App\Models\RestaurantsPayout;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class PayoutHistoryDataTable extends DataTable
{

    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('payout_period', function ($payout) {
                return date('d M Y', strtotime($payout->from_date)) . ' - ' . date('d M Y', strtotime($payout->to_date)); 
            })
            ->editColumn('gross_revenue', function ($payout) {
                return getPriceColumn($payout, 'gross_revenue');
            })
            ->editColumn('commision_tax', function ($payout) {
                $taxRate = $payout->tax;
                $comRate = $payout->admin_commission;
                $commission = ($comRate / 100) * $payout->gross_revenue;
                $taxTotal = ($taxRate / 100) * $commission;
                return getPrice($commission) . " ($comRate%) / " . getPrice($taxTotal) . " ($taxRate%)";
            })
            ->editColumn('amount', function ($payout) {
                return getPriceColumn($payout, 'amount');
            })
            ->editColumn('created_at', function ($payout) {
                return getDateColumn($payout, 'created_at');
            })
            ->addColumn('action', function($payout) { 
                return "<button>Hello</button>";
            })
            ->rawColumns(array_merge($columns, ['action']));

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
                'data' => 'restaurant.name',
                'title' => 'Restaurant',
                'orderable' => false
            ],
            [
                'data' => 'payout_period',
                'title' => 'Payout Period',
                'orderable' => false,
                'searchable' => false
            ],
            [
                'data' => 'orders',
                'title' => 'Orders',
                'orderable' => false

            ],
            [
                'data' => 'gross_revenue',
                'title' => 'Gross Revenue',
                'orderable' => false

            ],
            [
                'data' => 'commision_tax',
                'title' => 'Commission / Tax',
                'orderable' => false,
                'searchable' => false

            ],
            [
                'data' => 'amount',
                'title' => 'Amount Paid',
                'orderable' => false
            ],
            [
                'data' => 'created_at',
                'title' => 'Paid On'
            ]
        ];

        return $columns;
    }

    public function query(RestaurantsPayout $model)
    {
        if (auth()->user()->hasRole('manager')) {
            return $model->newQuery()->with("restaurant")->join('user_restaurants','user_restaurants.restaurant_id','=','restaurants_payouts.restaurant_id')
                ->where('user_restaurants.user_id',auth()->id())->select('restaurants_payouts.*');
        }
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
            ->parameters(array_merge(
                config('datatables-buttons.parameters'), [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ), true)
                ]
            ));
    }

    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }

    
    protected function filename()
    {
        return 'payout_history_' . time();
    }
}