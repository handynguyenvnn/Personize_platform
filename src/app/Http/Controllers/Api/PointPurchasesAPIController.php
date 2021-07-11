<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\PointPurchasesTransactionService;
use App\Http\Requests\DepositFormRequest;
use Exception;
use DB;

class PointPurchasesAPIController extends Controller
{
    protected $transactionService;

    public function __construct(PointPurchasesTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function deposit(DepositFormRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->transactionService->deposit($request->all());
            DB::commit();

            return responseOK($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage());
        }
    }

    public function executeDeposit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transactions,id'
        ]);

        DB::beginTransaction();
        try {
            $data = $this->transactionService->executeDeposit($request->id);
            DB::commit();

            return responseOK($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage());
        }
    }

    public function cancelDeposit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transactions,id'
        ]);

        DB::beginTransaction();
        try {
            $data = $this->transactionService->cancelDeposit($request->id);
            DB::commit();

            return responseOK($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage());
        }
    }

    public function listPackage(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->transactionService->listPackage();
            DB::commit();

            return responseOK($data);
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error($ex);
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage());
        }
    }
}
