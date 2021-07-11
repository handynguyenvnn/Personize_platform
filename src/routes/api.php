<?php

use App\Http\Controllers\TestController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::get('test', [TestController::class, 'index'])->name('test.controller');

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::post('logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
    Route::get('me', [\App\Http\Controllers\Auth\AuthController::class, 'me']);

});
//Socialite
Route::post('/login/{provider}', [\App\Http\Controllers\Auth\AuthController::class, 'checkUser']);

//sign-up
Route::get('/user/test', [\App\Http\Controllers\Api\UserController::class, 'testEmail']);

Route::post('/user/sign-up', [\App\Http\Controllers\Api\UserController::class, 'signUp']);
Route::post('/user/verification-email/{token}', [\App\Http\Controllers\Api\UserController::class, 'verificationEmail']);

//forgot-password
Route::post('/user/forgot-password', [\App\Http\Controllers\Api\UserController::class, 'forgotPassword']);
Route::put('/user/reset-password/{token}', [\App\Http\Controllers\Api\UserController::class, 'resetPassword']);
Route::get('/user/detail', [\App\Http\Controllers\Api\UserController::class, 'profile']);
Route::get('/banner-the-day', [\App\Http\Controllers\Api\UserController::class, 'getAdvertisementTheDay']);
Route::post('/user/contact', [\App\Http\Controllers\Api\UserController::class, 'sendMailToAdmin']);
Route::get('/stream/detail/{id}', [\App\Http\Controllers\Api\EventController::class, 'show']);
Route::get('/streamer/followers', [\App\Http\Controllers\Api\UserController::class, 'followers']);
Route::get('/streamer/following', [\App\Http\Controllers\Api\UserController::class, 'following']);
Route::get('/stream/detail/{id}', [\App\Http\Controllers\Api\EventController::class, 'show']);

Route::get('/get-country', [\App\Http\Controllers\Api\CountryController::class, 'getList']);
Route::get('/search-hashtag', [\App\Http\Controllers\Api\HashtagController::class, 'searchHashtag']);


Route::middleware(['auth:api'])->group(function () {
    //stream
    Route::post('/stream/create', [\App\Http\Controllers\Api\EventController::class, 'create']);
    //follow user
    Route::post('/user/follow', [\App\Http\Controllers\Api\UserController::class, 'follow']);
    Route::post('/user/un-follow', [\App\Http\Controllers\Api\UserController::class, 'unFollow']);

    Route::get('user/followers', [\App\Http\Controllers\Api\UserController::class, 'followers']);
    Route::get('user/following', [\App\Http\Controllers\Api\UserController::class, 'following']);
    //change profile
    Route::post('/user/change-profile', [\App\Http\Controllers\Api\UserController::class, 'changeProfile']);
    Route::post('/user/banking', [\App\Http\Controllers\Api\UserController::class, 'updateBanking']);
    Route::post('/user/withdraw-request', [\App\Http\Controllers\Api\UserController::class, 'withdrawRequest']);
    Route::get('/user/withdraw-list', [\App\Http\Controllers\Api\UserController::class, 'withdrawList']);
    Route::post('/user/withdraw-search', [\App\Http\Controllers\Api\UserController::class, 'withdrawSearch']);
    Route::get('/user/profile', [\App\Http\Controllers\Api\UserController::class, 'profile']);
    Route::post('/user/transaction-search', [\App\Http\Controllers\Api\UserController::class, 'transactionSearch']);

    //change password
    Route::post('/user/change-password', [\App\Http\Controllers\Api\UserController::class, 'changePassword']);

    // update event
    Route::put('/stream/{id}', [\App\Http\Controllers\Api\EventController::class, 'update']);

    //subscribe event
    Route::post('/event/subscribe', [\App\Http\Controllers\Api\EventController::class, 'subscribe']);
    Route::post('/event/un-subscribe', [\App\Http\Controllers\Api\EventController::class, 'unSubscribe']);

    //read notification
    Route::post('/notifications/read', [\App\Http\Controllers\Api\NotificationController::class, 'read']);

    //get transactions
    Route::get('/transactions', [\App\Http\Controllers\Api\TransactionsController::class, 'getTransactions']);

    Route::get('/notification-action', [\App\Http\Controllers\Api\NotificationController::class, 'notificationAction']);
});

// admin accessable routes
Route::group(['middleware' => ['auth:api', 'role:' . User::USER_ROLE_ADMIN]], function () {
    // dashboard api
    Route::get('/dashboard/initial-fetch', [\App\Http\Controllers\Api\AdminController::class, 'initialFetch']);

    // settings api
    Route::get('/configurations', [\App\Http\Controllers\Api\AdminController::class, 'getConfigurations']);
    Route::put('/configurations', [\App\Http\Controllers\Api\AdminController::class, 'updateConfigurations']);
    Route::get('/bank-settings', [\App\Http\Controllers\Api\AdminController::class, 'getBankAccounts']);
    Route::post('/bank-settings/create', [\App\Http\Controllers\Api\AdminController::class, 'createRootAdminBankAccount']);
    Route::get('/managers', [\App\Http\Controllers\Api\AdminController::class, 'getManagers']);
    Route::get('/managers/promotable', [\App\Http\Controllers\Api\AdminController::class, 'getPromotableUsers']);
    Route::put('/managers/promote/{id}', [\App\Http\Controllers\Api\AdminController::class, 'promoteToManager']);
    Route::put('/managers/demote/{id}', [\App\Http\Controllers\Api\AdminController::class, 'removeManagerStatus']);
    Route::get('/countries-prefectures', [\App\Http\Controllers\Api\AdminController::class, 'getCountriesAndPrefectures']);

    // user api
    Route::get('/user', [\App\Http\Controllers\Api\AdminController::class, 'getUsers']);
    Route::get('/user/{id}', [\App\Http\Controllers\Api\AdminController::class, 'getUserById']);
    Route::post('/user/create', [\App\Http\Controllers\Api\AdminController::class, 'createUser']);
    Route::put('/user/{id}', [\App\Http\Controllers\Api\AdminController::class, 'updateUser']);
    Route::delete('/user/{id}', [\App\Http\Controllers\Api\AdminController::class, 'deleteUser']);
    Route::get('/user/{id}/bank-details', [\App\Http\Controllers\Api\AdminController::class, 'getBankDetailsByUserId']);
    Route::get('/user/{id}/transactions', [\App\Http\Controllers\Api\AdminController::class, 'getTransactionsByUserId']);

    // bank api
    Route::get('/bank-details/{id}', [\App\Http\Controllers\Api\AdminController::class, 'getBankAccountById']);
    Route::put('/bank-details/{id}', [\App\Http\Controllers\Api\AdminController::class, 'updateBankDetails']);
    Route::delete('/bank-details/{id}', [\App\Http\Controllers\Api\AdminController::class, 'deleteBankDetails']);

    // stream/event api
    Route::get('/stream', [\App\Http\Controllers\Api\AdminController::class, 'getEvents']);
    Route::get('/stream-admin/{id}', [\App\Http\Controllers\Api\AdminController::class, 'getEventById']);
    Route::delete('/stream/{id}', [\App\Http\Controllers\Api\AdminController::class, 'deleteEvent']);

    // transactions api
    Route::get('/transactions/all', [\App\Http\Controllers\Api\AdminController::class, 'getTransactions']);
    Route::get('/transactions/{id}', [\App\Http\Controllers\Api\AdminController::class, 'getTransactionById']);
    Route::get('/point-purchases/all', [\App\Http\Controllers\Api\AdminController::class, 'getPointPurchases']);
    Route::get('/event-payments/all', [\App\Http\Controllers\Api\AdminController::class, 'getEventPayments']);
    Route::get('/withdraw-transactions/all', [\App\Http\Controllers\Api\AdminController::class, 'getWithdrawTransactions']);
    Route::get('/refunds/all', [\App\Http\Controllers\Api\AdminController::class, 'getRefunds']);
    Route::get('/point-adjustments/all', [\App\Http\Controllers\Api\AdminController::class, 'getPointAdjustments']);

    // event reports api
    Route::get('/events/reports', [\App\Http\Controllers\Api\AdminController::class, 'getEventReports']);
    Route::get('/events/reports/unread', [\App\Http\Controllers\Api\AdminController::class, 'getAmountUnreadEventReports']);
    Route::put('/events/reports/unread', [\App\Http\Controllers\Api\AdminController::class, 'setEventReportsReadStatus']);
    Route::put('/events/reports/ban/{id}', [\App\Http\Controllers\Api\AdminController::class, 'banEvent']);
    Route::get('/events/reports/{id}', [\App\Http\Controllers\Api\AdminController::class, 'getEventReportById']);
    Route::delete('/events/reports/{id}', [\App\Http\Controllers\Api\AdminController::class, 'deleteEventReport']);

    // withdraw requests api
    Route::get('/withdraw-requests', [\App\Http\Controllers\Api\AdminController::class, 'getWithdrawRequests']);
    Route::get('/withdraw-requests/unread', [\App\Http\Controllers\Api\AdminController::class, 'getAmountUnreadWithdrawRequests']);
    Route::put('/withdraw-requests/unread', [\App\Http\Controllers\Api\AdminController::class, 'setWithdrawRequestsReadStatus']);
    Route::get('/withdraw-requests/{id}', [\App\Http\Controllers\Api\AdminController::class, 'getWithdrawRequestById']);
    Route::put('/withdraw-requests/{id}/status', [\App\Http\Controllers\Api\AdminController::class, 'setWithdrawRequestStatus']);
    Route::delete('/withdraw-requests/{id}', [\App\Http\Controllers\Api\AdminController::class, 'deleteWithdrawRequest']);

    // advertisement api
    Route::get('/ad-banners', [\App\Http\Controllers\Api\AdminController::class, 'getAdvertisements']);
    Route::post('/ad-banners/create', [\App\Http\Controllers\Api\AdminController::class, 'createAdvertisement']);
    Route::get('/ad-banners/{id}', [\App\Http\Controllers\Api\AdminController::class, 'getAdvertisementById']);
    Route::put('/ad-banners/{id}', [\App\Http\Controllers\Api\AdminController::class, 'updateAdvertisement']);
    Route::delete('/ad-banners/{id}', [\App\Http\Controllers\Api\AdminController::class, 'deleteAdvertisement']);
});

//get category
Route::get('/get-category', [\App\Http\Controllers\Api\CategoryController::class, 'getCategory']);
Route::get('/get-category-menu', [\App\Http\Controllers\Api\CategoryController::class, 'getCategoryMenu']);
//list stream (not auth)
Route::get('/stream/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'listEvent']);
Route::get('/event/banner', [\App\Http\Controllers\Api\EventController::class, 'getBanner']);

//get notifications
Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'getNotifications']);

//search event, streamer
Route::get('/search', [\App\Http\Controllers\Api\EventController::class, 'searchEventOrStreamer']);

//search banner
Route::get('/banners', [\App\Http\Controllers\Api\BannerController::class, 'checkEventBanners']);
//Event
Route::group(['prefix' => 'events'], function ($router) {
    Route::get('/detail/{id}', [\App\Http\Controllers\Api\EventController::class, 'detailEvent']);
    Route::get('/', [\App\Http\Controllers\Api\EventController::class, 'checkUserAccessEvent']);
    Route::get('/lists', [\App\Http\Controllers\Api\EventController::class, 'getListEvent']);
    Route::post('/', [\App\Http\Controllers\Api\EventController::class, 'create']);
    Route::post('/reports', [\App\Http\Controllers\Api\EventController::class, 'createReport']);
    Route::post('/cancel', [\App\Http\Controllers\Api\EventController::class, 'userCancelEvent']);
    Route::post('/update/{id}', [\App\Http\Controllers\Api\EventController::class, 'update']);
});

//search users
Route::get('/users/search', [\App\Http\Controllers\Api\UserController::class, 'searchUsers']);

//user banking
Route::get('/users/profile/bankings', [\App\Http\Controllers\Api\UserController::class, 'userBank']);

Route::post('/payment/deposit', [\App\Http\Controllers\Api\PointPurchasesAPIController::class, 'deposit']);
Route::get('/payment/package', [\App\Http\Controllers\Api\PointPurchasesAPIController::class, 'listPackage']);

// Route::put('/payment/deposit/execute', [\App\Http\Controllers\Api\PointPurchasesAPIController::class, 'executeDeposit']);
// Route::put('/payment/deposit/cancel', [\App\Http\Controllers\Api\PointPurchasesAPIController::class, 'cancelDeposit']);
//stripe hook
Route::post('/stripe/web-hook/deposit', [\App\Http\Controllers\Api\WebHookStripeAPIController::class, 'depositWebHook']);
