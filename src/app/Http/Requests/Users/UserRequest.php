<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(Request $request)
    {
        $id = $this->route('id');
        $rules = [
            'email' => ['required', 'max: 100', 'email',
                Rule::unique('users')->ignore($id)->where(function ($query) {
                    return $query->whereNull('deleted_at')->whereNull('provider')->get();
                })
            ],
            'password' => 'required|confirmed|min:6|max:16',
            'nick_name' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNull('deleted_at')->get();
                })
            ]
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'email'     => 'メール',
            'password'  => 'パスワード',
            'nick_name' => 'ニックネーム'
        ];
    }
}
