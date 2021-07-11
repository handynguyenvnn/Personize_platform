<?php

namespace App\Http\Controllers\Api;

use App\Consts;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\FollowUserRequest;
use App\Http\Requests\Users\ResetPasswordRequest;
use App\Http\Resources\ListUserCollection;
use App\Http\Resources\ListUserFollowingCollection;
use App\Http\Resources\UserResource;
use App\Mail\MailVerificationMail;
use App\Mail\ContactMail;
use App\Mail\ResetPassword;
use App\Mail\WithDrawNotifications;
use App\Models\Userbank;
use App\Models\WithdrawRequest;
use App\Repositories\BannerRepository;
use App\Repositories\EventRepository;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use App\Repositories\VerificationEmailRepository;
use App\Services\FileService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    protected $userRepository;
    protected $passwordResetRepository;
    protected $eventRepository;
    protected $verificationEmailRepository;
    protected $bannerRepository;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetRepository $passwordResetRepository,
        EventRepository $eventRepository,
        VerificationEmailRepository $verificationEmailRepository,
        BannerRepository $bannerRepository
    ) {
        $this->userRepository = $userRepository;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->eventRepository = $eventRepository;
        $this->verificationEmailRepository = $verificationEmailRepository;
        $this->bannerRepository = $bannerRepository;
    }
    public function testEmail(Request $request)
    {
        try {
            Log::debug("test email");
            // Mail::raw('Hello 111World!', function ($msg) {$msg->to('quynhdh09@gmail.com')->subject('Test Email');});
            Mail::to('quynhdh09@gmail.com')->send(new MailVerificationMail('aaa', 'def'));
            return responseOK('ok11111111');

        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
    public function signUp(Request $request)
    {
        try {

            $checkNicknameExisted = $this->userRepository->checkNicknameExisted($request);
            if ($checkNicknameExisted) {
                $checkMailExisted = $this->userRepository->checkMailExisted($request);
                if ($checkMailExisted) {
                    $user = $this->userRepository->signUp($request);
                    $tokenVerification = $this->verificationEmailRepository->create([
                        'email' => $user->email,
                        'token' => Str::random(60),
                    ]);
                    Log::debug("token" . $tokenVerification);
                    if ($tokenVerification) {
                        Log::debug("call send mail");
                        Mail::to($user->email)->send(new MailVerificationMail($user, $tokenVerification->token));
                    }
                    return responseOK(__('message.verification_mail'));
                } else {
                    return responseOK(["status" => false, "msg" => 'message_server.email_register_existed']);
                }
            } else {
                return responseOK(["status" => false, "msg" => 'message_server.nickname_register_existed']);
            }
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function verificationEmail($token)
    {
        try {
            DB::beginTransaction();
            $verificationEmail = $this->verificationEmailRepository->findByToken($token);
            $expiredTime = config('mail.verification_email_expired_time');
            if (empty($verificationEmail) || Carbon::parse($verificationEmail->created_at)->addMinutes($expiredTime)->isPast()) {
                DB::table('verification_emails')->where('token', $token)->delete();
                return responseBadRequest(__('message.verification_email_token_error'));
            }
            $user = $this->userRepository->findByEmailUser($verificationEmail->email);
            $this->userRepository->update($user->id, ['email_verified_at' => now()]);
            DB::table('verification_emails')->where('token', $token)->delete();
            DB::commit();
            return responseOK(['message' => __('message.verification_email_success')]);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }

    }

    public function forgotPassword(Request $request)
    {
        try {
            $user = $this->userRepository->findByEmailUser($request->email);
            Log::debug("forgotPassword");
            if ($user) {
                $passwordReset = $this->passwordResetRepository->create([
                    'email' => $request->email,
                    'token' => Str::random(60),
                ]);
                if ($passwordReset) {
                    Mail::to($user->email)->send(new ResetPassword($passwordReset->token));
                }

            } else {
                return responseOK(["status" => false, "msg" => 'message_server.email_register_not_existed']);
            }
            return responseOK(['message' => __('message.reset_password')]);

        } catch (\Exception $exception) {

            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function resetPassword($token, Request $request)
    {
        try {
            $passwordReset = $this->passwordResetRepository->findByToken($token);
            $expiredTime = config('mail.mail_reset_password_expired_time');
            if (empty($passwordReset) || Carbon::parse($passwordReset->created_at)->addMinutes($expiredTime)->isPast()) {
                DB::table('password_resets')->where('token', $token)->delete();
                return responseBadRequest(__('passwords.token'));
            }
            $user = $this->userRepository->findByEmailUser($passwordReset->email);
            $this->userRepository->update($user->id, ['password' => Hash::make($request->password)]);
            DB::table('password_resets')->where('token', $token)->delete();

            return responseOK(['message' => __('passwords.reset')]);

        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function follow(FollowUserRequest $request)
    {
        try {
            $this->userRepository->follow($request);
            return responseOK(['message' => __('message.users.follow_user')]);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function unFollow(Request $request)
    {
        try {
            $this->userRepository->unFollow($request);
            return responseOK(['message' => __('message.users.un_follow_user')]);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function changeProfile(Request $request)
    {
        try {
            $data = $request->all();
            $fileSevice = new FileService(
                Config::get('filesystems.type_disks_upload'),
                Config::get('filesystems.disks_upload_path_avatar')
            );
            if (isset($request->avatar) && $request->avatar !== null) {

                // $filename = mt_rand() . "_" . microtime(true) . "_" . $request->avatar->getClientOriginalName();
                // $url = $fileSevice->uploadFile($filename, $request->avatar);
                // $data['avatar'] = $fileSevice->getFilePath($url);

                if (str_contains($request->avatar, config('app.app_url'))) {
                    $data['avatar'] = $request->avatar ? auth()->user()->avatar : null;
                } else {
                    $imageDecodeBase64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->avatar));
                    $filename = mt_rand() . "_" . microtime(true) . '.' . 'png';
                    $url = $fileSevice->uploadBase64File($filename, $imageDecodeBase64);

                    $data['avatar'] = $fileSevice->getFilePath($url);
                }
            } else {
                $data['avatar'] = config('app.default_avatar');
            }

            if (isset($data['bankings'])) {
                $input_bank = json_decode($data['bankings']);
                DB::table('users_bankings')->where('user_id', auth()->user()->id)->delete();
                foreach ($input_bank as $banking) {
                    DB::table('users_bankings')->insert([
                        'user_id' => auth()->user()->id,
                        'bank_name' => $banking->bank_name,
                        'branch_name' => $banking->branch_name,
                        'bank_account_number' => $banking->bank_account_number,
                        'bank_account_holder' => $banking->bank_account_holder,
                    ]);
                }
            }

            $checkNicknameExisted = $this->userRepository->checkNicknameEmailExisted($request);

            if($request->nick_name == auth()->user()->nick_name) {
                $profile = $this->userRepository->update(auth()->user()->id, $data);
                $user_banks = DB::table('users_bankings')->where('user_id', auth()->user()->id)->get();
                $profile->banking = $user_banks;
                
                return responseOK(new UserResource($profile));
            } else {
                if ($checkNicknameExisted) {
                    $profile = $this->userRepository->update(auth()->user()->id, $data);
                    $user_banks = DB::table('users_bankings')->where('user_id', auth()->user()->id)->get();
                    $profile->banking = $user_banks;
                    return responseOK(new UserResource($profile));
                } else {
                    return responseOK(["status" => false, "msg" => 'message_server.nickname_register_existed']);
                }
            }

            
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
    public function updateBanking(Request $request)
    {
        try {
            $updateBanking = DB::table('users_bankings')->create([
                'user_id' => auth()->user()->id,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_holder' => $request->bank_account_holder,
            ]);
            return responseOK($updateBanking);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
    public function withdrawRequest(Request $request)
    {
        try {
            $withdrawRequest = WithdrawRequest::create([
                'user_id' => auth()->user()->id,
                'code' => substr(md5(mt_rand()), 0, 10),
                'status' => Consts::WITHDRAW_REQUEST_STATUS_PENDING,
                'description' => $request->description,
                'amount' => $request->amount,
                'point' => $request->point,
            ]);
            $profile = $this->userRepository->profile($request);

            //send mail to admin
            Mail::to(env('ADMIN_EMAIL', 'adpersonize@gmail.com'))->bcc(env('BCC_GROUP_MAIL'))->send(new WithDrawNotifications($profile->email));

            return responseOK($withdrawRequest);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
    public function withdrawList(Request $request)
    {
        try {
            $withdrawList = WithdrawRequest::where('user_id', auth()->user()->id)->get();
            return responseOK($withdrawList);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
    public function transactionSearch(Request $request)
    {try {
        $query_str = "SELECT * FROM transactions WHERE user_id = " . auth()->user()->id;
        if (count($request->all()) > 0) {
            if ($request->type) {
                $query_str .= " AND type = '" . $request->type . "'";
            }
            if (($request->fromdate) && $request->enddate) {
                $query_str .= " AND (created_at >= '" . $request->fromdate . "' AND created_at <= '" . $request->enddate . " 23:59:59')";
            }
            if (!$request->fromdate) {
                $query_str .= " AND created_at <= '" . $request->enddate . " 23:59:59'";
            }
            if (!$request->enddate) {
                $query_str .= " AND created_at >= '" . $request->fromdate . "'";
            }
        }
        $transactionSearch = DB::select($query_str);
        return responseOK($transactionSearch);
    } catch (\Exception $exception) {
        return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
    }
    }
    public function withdrawSearch(Request $request)
    {
        try {
            $query_str = "SELECT * FROM withdraw_requests WHERE user_id = " . auth()->user()->id;
            if (count($request->all()) > 0) {
                if ($request->status) {
                    $query_str .= ' AND status = ' . $request->status;
                }
                if (($request->fromdate) && $request->enddate) {
                    $query_str .= " AND (created_at >= '" . $request->fromdate . "' AND created_at <= '" . $request->enddate . " 23:59:59')";
                }
                if (!$request->fromdate) {
                    $query_str .= " AND created_at <= '" . $request->enddate . " 23:59:59'";
                }
                if (!$request->enddate) {
                    $query_str .= " AND created_at >= '" . $request->fromdate . "'";
                }
            }
            $withdrawSearch = DB::select($query_str);
            return responseOK($withdrawSearch);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function profile(Request $request)
    {
        try {
            $profile = $this->userRepository->profile($request);
            if (!$request->is_edit) {
                $profile->event_comming = $this->eventRepository->eventUserSuggestion(true, $request);
                $profile->event_pass = $this->eventRepository->eventUserSuggestion(false, $request);
                $profile->my_event = $this->eventRepository->getEventOfUserSuggestion($request);
            }
            $profile->banking = $this->eventRepository->eventUserBanking($request);
            $profile->configuration = $this->eventRepository->getConfiguration();
            return responseOK(new UserResource($profile));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function userDetail(Request $request)
    {
        try {
            $profile = $this->userRepository->profile($request);
            if (!$request->is_edit) {
                $profile->event_comming = $this->eventRepository->eventUserSuggestion(true, $request);
                $profile->event_pass = $this->eventRepository->eventUserSuggestion(false, $request);
                $profile->my_event = $this->eventRepository->getEventOfUserSuggestion($request);
            }
            $profile->banking = $this->eventRepository->eventUserBanking($request);
            return responseOK(new UserResource($profile));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function changePassword(ResetPasswordRequest $request)
    {
        try {
            if (!Hash::check($request->current_password, auth()->user()->password)) {
                return responseValidate(
                    ['errors' => [__('message.password.current_password_incorrect')]]
                );
            }
            $this->userRepository->update(
                auth()->user()->id,
                ['password' => Hash::make($request->password)]
            );
            return responseOK(new UserResource(auth()->user()));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function followers(Request $request)
    {
        try {
            $users = $this->userRepository->getListFollowers($request);
            return responseOK(new ListUserCollection($users));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function following(Request $request)
    {
        try {
            $users = $this->userRepository->getListFollowing($request);
            return responseOK(new ListUserFollowingCollection($users));
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function searchUsers(Request $request)
    {
        try {
            $users = $this->userRepository->searchUsers($request);
            return responseOK($users);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function userBank(Request $request)
    {
        try {
            if (!empty($request->is_edit)) {
                $profile = $this->userRepository->profile($request);
                if ($profile) {
                    $user_bank = $this->userRepository->userBank($request);
                    return responseOK($user_bank);
                } else {
                    return [
                        'status' => 0,
                        'msg' => 'User not found',
                    ];
                }
            }

        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function getAdvertisementTheDay()
    {
        try {
            $advertisement = $this->bannerRepository->getAdvertisementTheDay();

            return responseOK($advertisement);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function sendMailToAdmin(Request $request)
    {
        try {
            $data = $request->all();
            if ($data) {
                Mail::to(env('ADMIN_EMAIL', 'adpersonize@gmail.com'))->bcc(env('BCC_GROUP_MAIL'))->send(new ContactMail($data));
            }
            return responseOK(["msg" => __('message.verification_mail'), "status" => true]);
        } catch (\Exception $exception) {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
