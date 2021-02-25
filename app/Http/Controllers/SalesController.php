<?php

namespace App\Http\Controllers;


use App\DataTables\SalesDataTable;


class SalesController extends Controller
{
 
    public function __construct()
    {
        parent::__construct();
    }

    public function index(SalesDataTable $salesDataTable)
    {
        return $salesDataTable->render('sales.index');
    }

}
