<?php
/**
 * File name: OrderDataTable.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Order;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class OrderDataTable extends DataTable
{
    /**
     * custom fields columns
     * @var array
     */
    public static $customFields = [];
    public $colors = ['blue', 'green', 'orange', 'orange', 'red', 'red'];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
       
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('id', function ($order) {
                return "#".$order->id;
            })
            ->editColumn('address', function ($order) {
                $address = isset($order->deliveryAddress) ? $order->deliveryAddress->address : 'NA';
                return $address;
            })
            ->editColumn('created_at', function ($order) {
                return getDateColumn($order, 'created_at');
            })
            ->editColumn('preorder_info', function($order) {
                $preorderInfo = $order->preorder_info;
                return getBooleanColumn(['preorder_info' => ($preorderInfo != null || $preorderInfo != '') ], 'preorder_info');
            })
            ->editColumn('order_status.status', function($order) {
                $statusId = $order->active ? $order->order_status_id : 0;
                $status = $order->active ? $order->orderStatus->status : 'Cancelled';
                $color = $this->colors[$statusId];
                return "<span style='color:$color;'><b>$status</b></span>";
            })
            ->addColumn('action', 'orders.datatables_actions')
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
                'data' => 'id',
                'name' => 'id',
                'title' => trans('lang.order_id'),

            ],
            [
                'data' => 'address',
                'name' => 'address',
                'title' => 'Address',
                'width' => '25%'
            ],
            /*[
                'data' => 'user.name',
                'name' => 'user.name',
                'title' => trans('lang.order_user_id'),

            ],*/
            [
                'data' => 'order_status.status',
                'name' => 'orderStatus.status',
                'title' => trans('lang.order_order_status_id'),

            ],
            /*[
                'data' => 'tax',
                'title' => trans('lang.order_tax'),
                'searchable' => false,

            ],*/
            /*[
                'data' => 'delivery_fee',
                'title' => trans('lang.order_delivery_fee'),
                'searchable' => false,

            ],*/
            /*[
                'data' => 'payment.status',
                'name' => 'payment.status',
                'title' => trans('lang.payment_status'),

            ],*/
            /*[
                'data' => 'payment.method',
                'name' => 'payment.method',
                'title' => trans('lang.payment_method'),

            ],*/
            [
                'data' => 'order_type',
                'name' => 'order_type',
                'title' => 'Type'
            ],
            [
                'data' => 'preorder_info',
                'name' => 'preorder_info',
                'title' => 'Pre-Order'
            ],
            /*[
                'data' => 'active',
                'title' => trans('lang.order_active'),

            ],*/
            [
                'data' => 'created_at',
                'title' => 'Created At',
                'searchable' => false,
                'orderable' => true,

            ]
        ];

        $hasCustomField = in_array(Order::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Order::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.order_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {   
        
        if (auth()->user()->hasRole('admin')) {
            $model = $model->newQuery()->with("user")->with("orderStatus")->with('payment');
            if ($this->showLiveOrders == 'true') $model = $model->whereRaw('orders.order_status_id < 5 and orders.active = 1');
            return $model->select("orders.*");
        } 
        else if (auth()->user()->hasRole('manager')) {

            $model = $model->newQuery()->with("user")->with("orderStatus")->with('payment')
                ->join("food_orders", "orders.id", "=", "food_orders.order_id")
                ->join("foods", "foods.id", "=", "food_orders.food_id")
                ->join("user_restaurants", "user_restaurants.restaurant_id", "=", "foods.restaurant_id")
                ->where('user_restaurants.user_id', auth()->id());

            if ($this->showLiveOrders == 'true') $model = $model->whereRaw('orders.order_status_id < 5 and orders.active = 1');    
            $model = $model->groupBy('orders.id')->select('orders.*');

            return $model;
        } 
        else if (auth()->user()->hasRole('client')) {
            return $model->newQuery()->with("user")->with("orderStatus")->with('payment')
                ->where('orders.user_id', auth()->id())
                ->groupBy('orders.id')
                ->select('orders.*');
        } 
        else if (auth()->user()->hasRole('driver')) {
            return $model->newQuery()->with("user")->with("orderStatus")->with('payment')
                ->where('orders.driver_id', auth()->id())
                ->groupBy('orders.id')
                ->select('orders.*');
        } 
        else {
            return $model->newQuery()->with("user")->with("orderStatus")->with('payment');
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
        return 'ordersdatatable_' . time();
    }
    
}