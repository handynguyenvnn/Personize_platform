<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class BankingRequest extends FormRequest
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
            'bank_name' => 'required',
            'bank_account_holder' => 'required',
            'bank_account_number' => 'nullable|integer'
        ];
    }

    public function attributes()
    {
        return [
            'bank_name'       => '銀行名',
            'bank_account_holder'       => '銀行口座保有者',
            'bank_account_number' => '銀行の口座番号'
        ];
    }
}
