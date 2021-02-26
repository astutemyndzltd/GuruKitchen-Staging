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
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
                    ->editColumn('id', function ($order) {
                        return "#".$order->id;
                    })
                    ->editColumn('created_at', function ($order) {
                        return getDateColumn($order, 'updated_at');
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
                "title" => "Id"
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
            ]
        ];

        return $columns;
    }

    public function query(Order $model) 
    {
        return $model->newQuery()->with("user")->with("orderStatus")->with('payment')->select('orders.*');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
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