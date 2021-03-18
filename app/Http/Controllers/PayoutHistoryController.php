<?php

namespace App\Http\Controllers;

use App\DataTables\PayoutHistoryDataTable;
use PdfService;

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
        $pdfService = new PdfService();
        return $pdfService->render('payout_history.invoice', []);
    }

}
