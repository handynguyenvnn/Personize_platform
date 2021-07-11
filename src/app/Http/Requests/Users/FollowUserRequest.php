<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FollowUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'user_follow_id' => [
                'required',
                Rule::notIn(auth()->user()->id),
                Rule::unique('user_follow')->where(function ($query) use($request) {
                    $query->where('user_follow_id', $request->user_follow_id)
                        ->where('user_id', auth()->user()->id)
                        ->whereNull('deleted_at');
                }),
            ]
        ];
    }
}
