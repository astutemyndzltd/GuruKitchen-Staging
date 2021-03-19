<?php

namespace App\Http\Controllers;

use App\DataTables\PayoutHistoryDataTable;
use App\Helpers\PdfService;
use App\Repositories\RestaurantsPayoutRepository;

class PayoutHistoryController extends Controller
{
 
    private $payoutRepository;
    
    public function __construct(RestaurantsPayoutRepository $repository)
    {
        parent::__construct();
        $this->payoutRepository = $repository;
    }

    public function index(PayoutHistoryDataTable $dataTable)
    {
        return $dataTable->render('payout_history.index');
    }

    public function getInvoice($id) 
    {
        $payout = $this->payoutRepository->find($id);
        $pdfService = new PdfService();
        return $pdfService->render('payout_history.invoice', ['payout' => $payout]);
    }

}
