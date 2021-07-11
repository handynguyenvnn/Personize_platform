<?php

namespace App\Repositories;

use App\Consts;
use App\Models\User;
use App\Models\UserBank;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{

    protected $perPageUser = 18;

    public function getModel()
    {
        return User::class;
    }

    public function signUp($request)
    {

        return $this->model->create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nick_name' => $request->nick_name
        ]);
    }

    public function checkNicknameExisted($request, $nickName = null) {
        $param = $nickName !== null ? $nickName : $request->nick_name;
        $checkNicknameExisted = $this->model->where('nick_name', $param)->first();
        if($checkNicknameExisted) {
            return false;
        }
        return true;
    }

    public function checkMailExisted($request) {
        $checkMailExisted = $this->model->where('email', $request->email)->first();
        if($checkMailExisted) {
            return false;
        }
        return true;
    }

    public function checkNicknameEmailExisted($request) {
        $user = $this->model->where('email', $request->email)->first();
        if(isset($user) && ($user['nick_name'] == $request->nick_name)) {
            return true;
        } else {
            $checkNicknameExisted = $this->model->where('nick_name', $request->nick_name)->get();
            if(isset($checkNicknameExisted) && count($checkNicknameExisted) > 0) {
                return false;
            }
            return true;
        }

        
    }

    public function findByEmailUser($email) {
        return $this->model->whereEmail($email)->whereNull('provider')->first();
    }

    public function follow($request) {
        return auth()->user()->followPeople()->attach($request->user_follow_id);
    }

    public function unFollow($request) {
        return auth()->user()->followPeople()->whereUserFollowId($request->user_follow_id)->update([
            'user_follow.deleted_at' => now()
        ]);
    }

    public function profile($request) {
        $userId = isset($request->id) ? $request->id : auth()->user()->id;
        return $this->model->with('countryUser')->with('prefectureUser')->withCount('followMe', 'followPeople')->findOrFail($userId);

    }

    public function getListFollowers($request)
    {
        $userId = isset($request->id) ? $request->id : auth()->user()->id;
        return $this->model
            ->with(['followMe' => function($query) use ($userId) {
                $query->where('user_follow.user_id', $userId);
            }])
            ->whereHas('followOfMe', function ($query) use ($userId) {
                $query->where('user_follow_id', $userId);
            })
            ->paginate($request->limit ? $request->limit : $this->perPageUser);
    }

    public function getListFollowing($request)
    {
        $userId = isset($request->id) ? $request->id : auth()->user()->id;
        return $this->model
            ->whereHas('followPeopleOfMe', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->paginate($request->limit ? $request->limit : $this->perPageUser);
    }

    public function searchUsers($request) {
        if(!empty($request)) {
            $userData = $this->model ->where('name', 'like', '%'. $request->u . '%')
            ->orWhere('nick_name', 'like', '%'. $request->u . '%')
            ->orderByDesc('created_at')->get();
    
            return  [
                'userData' => $userData,
            ];
        }else{
            return [
                'status' => 0,
                'msg' => 'Username not null',
                'userData' => null
            ];
        }
    }

    public function userBank($request) {
        $userBankData = UserBank::where('user_id', $request->is_edit)->get();
        return  [
            'userBankData' => $userBankData,
        ];
    }

    // for admin
    public function getUsers($request) {
        return $this->model
            ->withTrashed()
            ->withCount('followMe', 'followPeople')
            ->orderByDesc('created_at')
            ->paginate($request->limit ? $request->limit : $this->perPageUser);
    }

    // for admin
    public function getById($id) {
        return $this->model->withTrashed()->withCount('followMe', 'followPeople')->findOrFail($id);
    }

    // for admin
    public function deleteUser($id) {
        return $this->model->findOrFail($id)->delete();
    }

    // for admin
    public function getDeletedUserById($id) {
        return $this->model->withTrashed()->findOrFail($id);
    }

    // for admin
    public function getBankDetailsByUserId($id) {
        return UserBank::where('user_id', $id)->first();
    }

    // for admin
    public function getRootAdminBankAccounts() {
        return UserBank::where('user_id', Consts::ROOT_ADMIN_ID)->get();
    }

    // for admin
    public function getBankAccountById($id) {
        return UserBank::where('id', $id)->first();
    }

    // for admin
    public function createRootAdminBankAccount($request) {
        $data = $request->all();
        $data = array_merge($data, ['user_id' => Consts::ROOT_ADMIN_ID]);
        UserBank::create($data);

        return UserBank::where('user_id', Consts::ROOT_ADMIN_ID)->get();
    }

    // for admin
    public function updateBankDetailsById($id, $data) {
        UserBank::where('id', $id)->update($data);
        
        return UserBank::where('id', $id)->first();
    }

    // for admin
    public function deleteBankDetailsById($id) {
        return UserBank::where('id', $id)->delete();
    }

    // for admin
    public function getManagers() {
        return $this->model
            ->withTrashed()
            ->where('role', User::USER_ROLE_MANAGER)
            ->orderByDesc('created_at')
            ->get();
    }

    // for admin
    public function setRole($id, $role) {
        return $this->model->where('id', $id)->update(['role' => $role]);
    }

    // for admin
    public function getPromotableUsers() {
        return $this->model
            ->where('role', 0)
            ->orWhere('role', NULL)
            ->orderByDesc('created_at')
            ->get();
    }
}
