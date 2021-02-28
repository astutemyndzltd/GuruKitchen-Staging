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
        file_put_contents('order.txt', "start -> $startDate | end -> $endDate");
        return $salesDataTable->render('sales.index');
    }

}
