<?php
/**
 * File name: OrderDataTable.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\DataTables;

use App\Models\Order;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class SalesDataTable extends DataTable 
{
    
    public function dataTable($query)
    {
       
        $dataTable = new EloquentDataTable($query);
        file_put_contents('order.txt', json_encode($dataTable));
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
                    ->editColumn('id', function ($order) {
                        return "#".$order->id;
                    })
                    ->editColumn('created_at', function ($order) {
                        return getDateColumn($order, 'created_at');
                    })
                    ->editColumn('price', function ($order) {
                        return 'xxxx';
                    })
                    ->editColumn('com_tax', function ($order) {
                        return 'xxxx';
                    })
                    ->editColumn('paid_out', function ($order) {
                        return 'xxxx';
                    })
                    ->addColumn('action', 'sales.datatables_actions')
                    ->rawColumns(array_merge($columns, ['action']));
        return $dataTable;
    }
    
    
    public function getColumns() 
    {
        $columns = [
            [
                "data" => "id",
                "name" => "id",
                "title" => "Order Id"
            ],
            [
                "data" => "created_at",
                "name" => "created_at",
                "title" => "Created At"
            ],
            [
                "data" => "price",
                "name" => "price",
                "title" => "Price"
            ],
            [
                "data" => "com_tax",
                "name" => "com_tax",
                "title" => "GuruKitchen Commission"
            ],
            [
                "data" => "paid_out",
                "name" => "paid_out",
                "title" => "Paid Out"
            ]
        ];

        return $columns;
    }

    public function query(Order $model) 
    {
        $start = $this->startDate;
        $end = $this->endDate;
             
        if (auth()->user()->hasRole('manager')) {

            $model = $model->newQuery()->with("user")->with("orderStatus")->with('payment')
                ->join("food_orders", "orders.id", "=", "food_orders.order_id")
                ->join("foods", "foods.id", "=", "food_orders.food_id")
                ->join("user_restaurants", "user_restaurants.restaurant_id", "=", "foods.restaurant_id")
                ->where('user_restaurants.user_id', auth()->id());

            $model = $model->whereRaw('orders.order_status_id = 5')->whereRaw("date(orders.created_at) between '$start' and '$end'");    
            $model = $model->groupBy('orders.id')->select('orders.*');

            return $model;
        }
        
        return $model;
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->ajax(['data' => 'function(d) { onReloadDt(d); }'])
            ->addAction(['title'=>trans('lang.actions'),'width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
            ->parameters(array_merge(
                [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ), true),
                    'order' => [ [0, 'desc'] ],
                ],
                config('datatables-buttons.parameters')
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
        return 'salesdatatable_' . time();
    }
}