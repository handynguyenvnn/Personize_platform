<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListTransactionCollection;
use App\Repositories\TransactionsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionsController extends Controller
{
    protected $transactionsRepository;

    public function __construct(TransactionsRepository $transactionsRepository)
    {
        $this->transactionsRepository = $transactionsRepository;
    }

    public function getTransactions(Request $request)
    {
        try {
            $transactions = $this->transactionsRepository->getList($request);
            return responseOK(new ListTransactionCollection($transactions));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
