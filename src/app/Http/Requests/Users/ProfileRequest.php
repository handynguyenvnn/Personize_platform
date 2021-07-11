<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
    public function rules()
    {
        return [
            'age' => 'nullable|integer',
            'sex' => 'nullable|integer',
            'nick_name' => [
                'required',
                Rule::unique('users')->ignore(auth()->user()->id)->where(function ($query) {
                    return $query->whereNull('deleted_at')->get();
                })
            ]
        ];
    }

    public function attributes()
    {
        return [
            'age'       => '年齢',
            'sex'       => '性別',
            'nick_name' => 'ニックネーム'
        ];
    }
}
