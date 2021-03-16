<?php

namespace App\Http\Controllers;

use App\DataTables\PayoutHistoryDataTable;
use Illuminate\Http\Request;
use App\DataTables\SalesDataTable;
use Barryvdh\DomPDF\Facade as PDF;


class PayoutHistoryController extends Controller
{
 
    public function __construct()
    {
        parent::__construct();
    }

    public function index(PayoutHistoryDataTable $dataTable)
    {
        return $dataTable->render('payout_history.index');
    }

    public function getInvoice($id) 
    {
        PDF::setOptions([]);
        $pdf = PDF::loadView('payout_history.invoice', []);
        return $pdf->download('invoice.pdf');
    }

}
