<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'age' => 'nullable|integer',
            'sex' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'prefecture_id' => 'nullable|integer',
            'email' => ['max: 100', 'email', Rule::unique('users')->ignore($id)->where(function ($query) {
                return $query->get();
            })],
            'nick_name' => [Rule::unique('users')->ignore($id)->where(function ($query) {
                    return $query->get();
                })],
            'password' => 'min:6|max:16'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'sex' => (int)$this->sex,
            'country_id' => (int)$this->country_id,
            'prefecture_id' => (int)$this->prefecture_id
        ]);
    }

    public function attributes()
    {
        return [
            'age' => '年齢',
            'sex' => '性別',
            'nick_name' => 'ニックネーム',
            'password' => 'パスワード'
        ];
    }
}
