<?php

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\RestaurantsPayout;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class RestaurantsPayoutDataTable extends DataTable
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
            ->editColumn('amount_paid', function ($payout) {
                return getPriceColumn($payout, 'amount');
            })
            ->rawColumns(array_merge($columns));

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

            ],
            [
                'data' => 'payout_period',
                'title' => 'Payout Period',

            ],
            [
                'data' => 'orders',
                'title' => 'Orders',

            ],
            [
                'data' => 'gross_revenue',
                'title' => 'Gross Revenue',

            ],
            [
                'data' => 'commision_tax',
                'title' => 'Commission / Tax',

            ],
            [
                'data' => 'amount_paid',
                'title' => 'Amount Paid',
            ],
            [
                'data' => 'note',
                'title' => 'Note',
            ]
        ];


        $hasCustomField = in_array(RestaurantsPayout::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', RestaurantsPayout::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.restaurants_payout_' . $field->name),
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
    public function query(RestaurantsPayout $model)
    {
        if(auth()->user()->hasRole('admin')){
            return $model->newQuery()->with("restaurant")->select('restaurants_payouts.*');
        }elseif (auth()->user()->hasRole('manager')){
            return $model->newQuery()->with("restaurant")->join('user_restaurants','user_restaurants.restaurant_id','=','restaurants_payouts.restaurant_id')
                ->where('user_restaurants.user_id',auth()->id())->select('restaurants_payouts.*');
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
            //->addAction(['width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
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
        return 'restaurants_payoutsdatatable_' . time();
    }
}