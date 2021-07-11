<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PointPurchasesTransactionService;

class WebHookStripeAPIController extends Controller
{
    private $transactionService;

    public function __construct(PointPurchasesTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function depositWebHook(Request $request)
    {
        $data = $this->transactionService->depositStripeWebHook($request->all());
    }
}
