<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\LoginFailed;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\TokenService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Socialite;
use Illuminate\Support\Str;

use App\Services\FileService;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    protected $tokenService;
    protected $userRepository;

    public function __construct(TokenService $tokenService, UserRepository $userRepository)
    {
        $this->tokenService = $tokenService;
        $this->userRepository = $userRepository;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = array_merge($request->only(['email', 'password']), ['provider' => User::USER_LOGIN_FORM]);
        $uuid = Uuid::uuid4();
        $token = auth('api')
            ->claims(['token_id' => $uuid])
            ->attempt($credentials);
        if (!$token) {
            throw new LoginFailed(__('message.login_fail'));
        }
        if (!auth()->user()->email_verified_at) {
            throw new LoginFailed(__('message.verification_email_error'));
        }

        return respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return responseOK(auth()->user());
    }

    public function logout()
    {
        $accessTokenId = auth('api')
            ->payload()
            ->get('token_id');
        $this->tokenService->clearRefreshToken($accessTokenId);
        auth('api')->logout();
        return responseOK();
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $payload = $this->tokenService
                ->refresh($request->bearerToken());
            $this->tokenService->saveTokenInCookie($payload['refresh_token']);
            return respondWithToken(
                $payload['access_token'],
                $payload['refresh_token']
            );
        } catch (\Exception $e) {
            throw new AuthenticationException(__('message.unauthenticated'));
        }
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function checkUser($provider, Request $request)
    {
        if ($provider == 'twitter') {
            $getInfo = Socialite::driver($provider)->userFromTokenAndSecret($request->token, $request->scret);
            $getInfo->avatar = str_replace('_normal.jpg', '.jpg', $getInfo->avatar);

        } else {
            $getInfo = Socialite::driver($provider)->stateless()->userFromToken($request->token);
            if ($provider == 'facebook') {
                $getInfo->avatar = $getInfo->avatar . "&access_token={$getInfo->token}";
            } else if ($provider == 'google') {
                $getInfo->avatar = str_replace('s96', 's1000', $getInfo->avatar);
            }
        }
        Log::debug("getInfo" . print_r($getInfo, true));

        if ($getInfo) {
            $data = $this->loginUser($getInfo, $provider);

            if ($data['status'] == true) {
                Log::debug("authToken" . print_r($data, true));
                $token = auth()->login($data['check_exist']);
                return respondWithToken($token);
            } else {
                return responseOK($data);
            }
        } else {
            return responseError(Response::HTTP_INTERNAL_SERVER_ERROR, 'Errors!');
        }

    }

    public function loginUser($getInfoUser, $provider)
    {
        Log::debug("provider" . print_r($getInfoUser, true));

        $check_exist = $this->userRepository->filterFirst([
            'provider' => $provider,
            'provider_id' => $getInfoUser->id,
        ]);

        if (!isset($check_exist)) {
            if ($provider == 'twitter' && isset($getInfoUser->nickname)) {
                $nickName = $getInfoUser->nickname;
            } else {
                $nickName = 'User' . Str::random(4) . strval(mt_rand(1, 99999999));
            }

            $checkNicknameExisted = $this->userRepository->checkNicknameExisted($getInfoUser, $nickName);
            if ($provider == 'twitter' && $checkNicknameExisted == false) {
                $nickName = 'User' . Str::random(4) . strval(mt_rand(1, 99999999));
                $checkNicknameExisted = $this->userRepository->checkNicknameExisted($getInfoUser, $nickName);

            }
            if ($checkNicknameExisted) {
                $checkMailExisted = $this->userRepository->checkMailExisted($getInfoUser);
                if ($checkMailExisted || !isset($getInfoUser->email)) {
                    $fileSevice = new FileService(
                        Config::get('filesystems.type_disks_upload'),
                        Config::get('filesystems.disks_upload_path_avatar')
                    );

                    $filename = mt_rand() . "_" . microtime(true) . '.' . 'png';
                    $url = $fileSevice->uploadFile($filename, $getInfoUser->avatar);
                    $avatarUrl = $fileSevice->getFilePath($url);

                    $check_exist = $this->userRepository->create(
                        [
                            'name' => $getInfoUser->name ? $getInfoUser->name : $getInfoUser->email,
                            'email' => $getInfoUser->email,
                            'avatar' => $avatarUrl,
                            'nick_name' => $nickName,
                            'provider' => $provider,
                            'provider_id' => $getInfoUser->id,
                        ]
                    );
                    return ["status" => true, "msg" => 'message_server.login_success', "check_exist" => $check_exist];
                } else {
                    return ["status" => false, "msg" => 'message_server.email_register_existed', "check_exist" => null];
                }
            } else {
                $this->loginUser($getInfoUser, $provider);
                return ["status" => false, "msg" => 'message_server.nickname_register_existed', "check_exist" => null];
            }
        } else {
            $length = strlen(config('filesystems.disks.' . config('filesystems.type_disks_upload') . '.url'));
            if (
                $check_exist->avatar &&
                (
                    substr($check_exist->avatar, 0, $length) ==
                    substr($getInfoUser->avatar, 0, $length)
                )
            ) {
                $check_exist->avatar = $getInfoUser->avatar;
                $check_exist->save();
            }

            return ["status" => true, "msg" => 'message_server.nickname_register_existed', "check_exist" => $check_exist];
        }

    }
}
