<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\SalesDataTable;


class SalesController extends Controller
{
 
    public function __construct()
    {
        parent::__construct();
    }

    public function index(SalesDataTable $salesDataTable, Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        return $salesDataTable->with(['startDate' => $startDate, 'endDate' => $endDate])->render('sales.index');
    }

}
