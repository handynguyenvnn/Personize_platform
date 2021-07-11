<?php

namespace App\Http\Controllers\Api;

use App\Consts;
use App\Models\User;
use App\Models\Country;
use App\Models\Prefecture;
use App\Http\Controllers\Controller;
use App\Http\Requests\BannerAdRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\UserRequest;
use App\Http\Resources\BannerAdResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\ListEventCollection;
use App\Http\Resources\ListUserCollection;
use App\Http\Resources\ListBannerAdCollection;
use App\Http\Resources\ListReportsCollection;
use App\Http\Resources\ListTransactionCollection;
use App\Http\Resources\ListPointPurchasesCollection;
use App\Http\Resources\ListEventPaymentCollection;
use App\Http\Resources\ListWithdrawRequestCollection;
use App\Http\Resources\ListWithdrawTransactionsCollection;
use App\Http\Resources\ListRefundCollection;
use App\Http\Resources\ListPointAdjustmentCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\ManagerResource;
use App\Http\Resources\UserBankResource;
use App\Http\Resources\ReportsResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\EventPaymentResource;
use App\Http\Resources\WithdrawRequestResource;
use App\Http\Resources\WithdrawTransactionsResource;
use App\Http\Resources\RefundResource;
use App\Http\Resources\PointAdjustmentResource;
use App\Http\Resources\ConfigurationResource;
use App\Http\Requests\Users\FollowUserRequest;
use App\Http\Requests\Users\ForgotPasswordRequest;
use App\Http\Requests\Users\ProfileRequest;
use App\Http\Requests\Users\ResetPasswordRequest;
use App\Repositories\BannerRepository;
use App\Repositories\EventRepository;
use App\Repositories\UserEventRepository;
use App\Repositories\UserRepository;
use App\Repositories\VerificationEmailRepository;
use App\Repositories\ReportsRepository;
use App\Repositories\TransactionsRepository;
use App\Repositories\PointPurchasesRepository;
use App\Repositories\EventPaymentRepository;
use App\Repositories\WithdrawRequestRepository;
use App\Repositories\WithdrawTransactionsRepository;
use App\Repositories\RefundRepository;
use App\Repositories\PointAdjustmentRepository;
use App\Repositories\ConfigurationRepository;
use App\Mail\WithdrawRequestAccepted;
use App\Mail\WithdrawRequestRefused;
use App\Mail\MailVerificationMail;
use App\Mail\EventBanned;
use App\Services\FileService;
use App\Services\WithdrawTransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    protected $userRepository;
    protected $eventRepository;
    protected $verificationEmailRepository;

    public function __construct(
        UserRepository $userRepository,
        EventRepository $eventRepository,
        UserEventRepository $userEventRepository,
        ReportsRepository $reportsRepository,
        BannerRepository $bannerRepository,
        TransactionsRepository $transactionsRepository,
        PointPurchasesRepository $pointPurchasesRepository,
        EventPaymentRepository $eventPaymentRepository,
        WithdrawRequestRepository $withdrawRequestRepository,
        WithdrawTransactionsRepository $withdrawTransactionsRepository,
        WithdrawTransactionService $withdrawTransactionService,
        RefundRepository $refundRepository,
        PointAdjustmentRepository $pointAdjustmentRepository,
        ConfigurationRepository $configurationRepository
    ) {
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->userEventRepository = $userEventRepository;
        $this->reportsRepository = $reportsRepository;
        $this->bannerRepository = $bannerRepository;
        $this->transactionsRepository = $transactionsRepository;
        $this->pointPurchasesRepository = $pointPurchasesRepository;
        $this->eventPaymentRepository = $eventPaymentRepository;
        $this->withdrawRequestRepository = $withdrawRequestRepository;
        $this->withdrawTransactionsRepository = $withdrawTransactionsRepository;
        $this->withdrawTransactionService = $withdrawTransactionService;
        $this->refundRepository = $refundRepository;
        $this->pointAdjustmentRepository = $pointAdjustmentRepository;
        $this->configurationRepository = $configurationRepository;
    }


    /* DASHBOARD FUNCTIONS - START */

    public function initialFetch(Request $request) {
        try {
            $configurations = $this->configurationRepository->getAll();
            $eventReportCount = $this->reportsRepository->getAmountUnreadReports();
            $withdrawRequestCount = $this->withdrawRequestRepository->getAmountUnreadWithdrawRequests();
            
            return responseOK([
                'withdrawRequestCount' => $withdrawRequestCount,
                'eventReportCount' => $eventReportCount,
                'configurations' => $configurations,
                'countries' => Country::get(),
                'prefectures' => Prefecture::get(),
            ]);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /* DASHBOARD FUNCTIONS - END */


    /* SETTINGS FUNCTIONS - START */

    public function getConfigurations(Request $request) {
        try {
            $configurations = $this->configurationRepository->getAll();

            return responseOK(ConfigurationResource::collection($configurations));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function updateConfigurations(Request $request) {
        try {
            $configurations = $this->configurationRepository->updateConfigurations($request->all());

            return responseOK(ConfigurationResource::collection($configurations));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getBankAccounts(Request $request) {
        try {
            $bankAccounts = $this->userRepository->getRootAdminBankAccounts();

            return responseOK(UserBankResource::collection($bankAccounts));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getBankAccountById($id, Request $request) {
        try {
            $bankAccount = $this->userRepository->getBankAccountById($id);

            if($bankDetails && $bankAccount['id'])
                return responseOK(new UserBankResource($bankAccount));
            else
                return responseOK('銀行情報がありません');
                
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function createRootAdminBankAccount(Request $request) {
        try {
            $bankAccounts = $this->userRepository->createRootAdminBankAccount($request);

            return responseOK(UserBankResource::collection($bankAccounts));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getManagers() {
        try {
            $managers = $this->userRepository->getManagers();

            return responseOK(ManagerResource::collection($managers));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getPromotableUsers(Request $request) {
        try {
            $users = $this->userRepository->getPromotableUsers();

            return responseOK(ManagerResource::collection($users));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function promoteToManager($id, Request $request) {
        try {
            $this->userRepository->setRole($id, User::USER_ROLE_MANAGER);

            return $this->getManagers();
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function removeManagerStatus($id, Request $request) {
        try {
            $this->userRepository->setRole($id, 0);

            return $this->getManagers();
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /* CONFIGURATION FUNCTIONS - END */


    /* USER FUNCTIONS - START */

    public function getUsers(Request $request)
    {
        try {
            $users = $this->userRepository->getUsers($request);
            return responseOK(new ListUserCollection($users));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getUserById($id, Request $request)
    {
        try {
            $user = $this->userRepository->getById($id);
            $user->my_event = $this->eventRepository->getEventsByUserId($id, $request);

            return responseOK(new UserResource($user));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function createUser(UserRequest $request)
    {
        try {
            $data = $request->all();
            if ($request->hasFile('avatar')) {
                $fileSevice = new FileService(
                    Config::get('filesystems.type_disks_upload'),
                    Config::get('filesystems.disks_upload_path_avatar')
                );
                $filename = mt_rand() . "_" . microtime(true) . "_" . $request->avatar->getClientOriginalName();
                $url = $fileSevice->uploadFile($filename, $request->avatar);

                $data['avatar'] = $fileSevice->getFilePath($url);
            }
            $data['password'] = Hash::make($data['password']);
            $data = array_merge($data, ['email_verified_at' => now()]);

            $user = $this->userRepository->create($data);

            return responseOK(new UserResource($user));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function updateUser($id, UpdateUserRequest $request)
    {
        try {
            $data = $request->all();

            if ($request->hasFile('avatar')) {
                $fileSevice = new FileService(
                    Config::get('filesystems.type_disks_upload'),
                    Config::get('filesystems.disks_upload_path_avatar')
                );
                $filename = mt_rand() . "_" . microtime(true) . "_" . $request->avatar->getClientOriginalName();
                $url = $fileSevice->uploadFile($filename, $request->avatar);

                $data['avatar'] = $fileSevice->getFilePath($url);

                // delete previous avatar
                $fileSevice->deleteFile(basename($this->userRepository->getById($id)->avatar));
            }
            if(isset($data['password']))
                $data['password'] = Hash::make($data['password']);

            $user = $this->userRepository->update($id, $data);

            return responseOK(new UserResource($user));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function deleteUser($id, Request $request)
    {
        try {
            $this->userRepository->deleteUser($id);
            $deleted_user = $this->userRepository->getDeletedUserById($id);

            return responseOk(new UserResource($deleted_user));
            // return responseOK([
            //     "message" => "ユーザーが削除されました"
            //   ]);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }


    public function getBankDetailsByUserId($id, Request $request) {
        try {
            $bankDetails = $this->userRepository->getBankDetailsByUserId($id);

            if($bankDetails && $bankDetails['id'])
                return responseOK(new UserBankResource($bankDetails));
            else
                return responseOK('銀行情報がありません');
                
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function updateBankDetails($id, Request $request) {
        try {
            $data = $request->except(['_method' ]);

            $bankDetails = $this->userRepository->updateBankDetailsById($id, $data);
            
            return responseOK(new UserBankResource($bankDetails));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function deleteBankDetails($id, Request $request) {
        try {
            $this->userRepository->deleteBankDetailsById($id);
            
            return responseOk('銀行情報が削除されました');
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getCountriesAndPrefectures(Request $request) {
        try {
            return responseOK([
                'countries' => Country::get(),
                'prefectures' => Prefecture::get(),
            ]);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /* USER FUNCTIONS - END */


    /* EVENT FUNCTIONS - START */

    public function getEvents(Request $request) {
        try {
            $events = $this->eventRepository->getEvents($request);
            return responseOK(new ListEventCollection($events));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getEventById($id, Request $request)
    {
        try {
            $detail_event = $this->eventRepository->detail($id, $request, true);
            return responseOK(new EventResource($detail_event));
        } catch (\Exception $e) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function deleteEvent($id, Request $request)
    {
        try {
            $this->eventRepository->deleteEvent($id);
            $deleted_event = $this->eventRepository->getDeletedEventById($id);

            return responseOk(new EventResource($deleted_event));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /* EVENT FUNCTIONS - END */


    /* EVENT REPORT FUNCTIONS - START */

    public function getEventReports(Request $request) {
        try {
            $reports = $this->reportsRepository->getReports($request);
            $reports->unread = $this->reportsRepository->getAmountUnreadReports($request)['unread'];

            return responseOK(new ListReportsCollection($reports));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getAmountUnreadEventReports(Request $request) {
        try {
            $res = $this->reportsRepository->getAmountUnreadReports();

            return responseOK($res);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function setEventReportsReadStatus(Request $request) {
        try {
            $this->reportsRepository->setEventReportsReadStatus($request);

            $res = $this->reportsRepository->getAmountUnreadReports();

            return responseOK($res);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getEventReportById($id, Request $request) {
        try {
            $report = $this->reportsRepository->getReportById($id);
                
            return responseOK(new ReportsResource($report));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function banEvent($id, Request $request) {
        try {
            $event = $this->reportsRepository->findOrFail($id)->event;
            if($event->status == 4) {
                return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, 'イベントはもう禁止されています。');
            }

            $this->userEventRepository->cancelEvent($event->id, 'イベントは禁止されました');

            Mail::to($event->userCreate->email)->send(new EventBanned($event->userCreate));

            return responseOK(new ReportsResource($this->reportsRepository->findOrFail($id)));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function deleteEventReport($id, Request $request) {
        try {
            $this->reportsRepository->delete($id);
                
            $reports = $this->reportsRepository->getReports($request);
            $reports->unread = $this->reportsRepository->getAmountUnreadReports($request)['unread'];

            return responseOK(new ListReportsCollection($reports));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /* EVENT REPORT FUNCTIONS - END */


    /* WITHDRAW REQUESTS FUNCTIONS - START */

    public function getWithdrawRequests(Request $request) {
        try {
            $withdrawRequests = $this->withdrawRequestRepository->getWithdrawRequests($request);
            $withdrawRequests->unread = $this->withdrawRequestRepository->getAmountUnreadWithdrawRequests($request)['unread'];

            return responseOK(new ListWithdrawRequestCollection($withdrawRequests));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getAmountUnreadWithdrawRequests(Request $request) {
        try {
            $res = $this->reportsRepository->getAmountUnreadWithdrawRequests();

            return responseOK($res);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function setWithdrawRequestsReadStatus(Request $request) {
        try {
            $this->withdrawRequestRepository->setWithdrawRequestsReadStatus($request);

            $res = $this->withdrawRequestRepository->getAmountUnreadWithdrawRequests();

            return responseOK($res);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getWithdrawRequestById($id, Request $request) {
        try {
            $withdrawRequest = $this->withdrawRequestRepository->getWithdrawRequestById($id);
                
            return responseOK(new WithdrawRequestResource($withdrawRequest));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function setWithdrawRequestStatus($id, Request $request) {
        try {
            $data = $request->all();

            // if status is accepted then perform the withdraw procedure. otherwise simply update the withdraw request
            if($data['status'] == Consts::WITHDRAW_REQUEST_STATUS_ACCEPTED) {
                $this->withdrawTransactionService->withdraw($id, $data);
            } else {
                $withdrawRequest = $this->withdrawRequestRepository->findOrFail($id);
                $withdrawRequest->update($data);

                if($data['status'] == Consts::WITHDRAW_REQUEST_STATUS_REJECTED) {
                    Mail::to($withdrawRequest->user->email)->send(new WithdrawRequestRefused($withdrawRequest->user));
                }
            }

            $withdrawRequest = $this->withdrawRequestRepository->getWithdrawRequestById($id);
            
            return responseOK([
                'data' => new WithdrawRequestResource($withdrawRequest),
                'unread' => $this->withdrawRequestRepository->getAmountUnreadWithdrawRequests()['unread']
            ]);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function deleteWithdrawRequest($id, Request $request) {
        try {
            $this->withdrawRequestRepository->delete($id);
                
            $withdrawRequests = $this->withdrawRequestRepository->getWithdrawRequests($request);
            $withdrawRequests->unread = $this->withdrawRequestRepository->getAmountUnreadWithdrawRequests($request)['unread'];

            return responseOK(new ListWithdrawRequestCollection($withdrawRequests));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /* WITHDRAW REQUESTS FUNCTIONS - END */


    /* ADVERTISEMENT FUNCTIONS - START */

    public function getAdvertisements(Request $request) {
        try {
            $advertisements = $this->bannerRepository->getAdvertisements($request);

            return responseOK(new ListBannerAdCollection($advertisements));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getAdvertisementById($id, Request $request)
    {
        try {
            $advertisement = $this->bannerRepository->getById($id);

            return responseOK(new BannerAdResource($advertisement));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function createAdvertisement(BannerAdRequest $request)
    {
        try {
            $data = $request->all();
            if ($request->hasFile('image')) {
                $fileSevice = new FileService(
                    Config::get('filesystems.type_disks_upload'),
                    Config::get('filesystems.disks_upload_path_ad_banner')
                );
                $filename = mt_rand() . "_" . microtime(true) . "_" . $request->image->getClientOriginalName();
                $url = $fileSevice->uploadFile($filename, $request->image);

                $data['image'] = $fileSevice->getFilePath($url);
            }
            $data['is_activated'] = true;

            $advertisement = $this->bannerRepository->create($data);

            return responseOK(new BannerAdResource($advertisement));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function updateAdvertisement($id, BannerAdRequest $request)
    {
        try {
            $data = $request->all();
            if ($request->hasFile('image')) {
                $fileSevice = new FileService(
                    Config::get('filesystems.type_disks_upload'),
                    Config::get('filesystems.disks_upload_path_ad_banner')
                );
                $filename = mt_rand() . "_" . microtime(true) . "_" . $request->image->getClientOriginalName();
                $url = $fileSevice->uploadFile($filename, $request->image);

                $data['image'] = $fileSevice->getFilePath($url);

                // delete previous image
                $fileSevice->deleteFile(basename($this->bannerRepository->getById($id)->image));
            }

            $advertisement = $this->bannerRepository->update($id, $data);

            return responseOK(new BannerAdResource($advertisement));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function deleteAdvertisement($id, Request $request)
    {
        try {
            $this->bannerRepository->deleteAdvertisement($id);
            $deleted_advertisement = $this->bannerRepository->getById($id);

            return responseOk(new BannerAdResource($deleted_advertisement));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /* ADVERTISEMENT FUNCTIONS - END */


    /* TRANSACTION FUNCTIONS - START */

    public function getTransactions(Request $request) {
        try {
            $transactions = $this->transactionsRepository->getTransactions($request);

            return responseOK(new ListTransactionCollection($transactions));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getTransactionById($id, Request $request) {
        try {
            $transaction = $this->transactionsRepository->getTransactionById($id);

            return responseOK(new TransactionResource($transaction));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getTransactionsByUserId($id, Request $request) {
        try {
            $transactions = $this->transactionsRepository->getTransactionsByUserId($id);

            return responseOK(new TransactionCollection($transactions));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getPointPurchases(Request $request) {
        try {
            $pointPurchases = $this->pointPurchasesRepository->getPointPurchases($request);

            return responseOK(new ListPointPurchasesCollection($pointPurchases));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getEventPayments(Request $request) {
        try {
            $eventPayments = $this->eventPaymentRepository->getEventPayments($request);

            return responseOK(new ListEventPaymentCollection($eventPayments));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getWithdrawTransactions(Request $request) {
        try {
            $withdrawTransactions = $this->withdrawTransactionsRepository->getWithdrawTransactions($request);

            return responseOK(new ListWithdrawTransactionsCollection($withdrawTransactions));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getRefunds(Request $request) {
        try {
            $refunds = $this->refundRepository->getRefunds($request);

            return responseOK(new ListRefundCollection($refunds));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getPointAdjustments(Request $request) {
        try {
            $pointAdjustments = $this->pointAdjustmentRepository->getPointAdjustments($request);

            return responseOK(new ListPointAdjustmentCollection($pointAdjustments));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /* TRANSACTION FUNCTIONS - END */

}
