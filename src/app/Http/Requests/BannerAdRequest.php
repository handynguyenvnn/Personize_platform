<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerAdRequest extends FormRequest
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
            'position' => 'required',
            'link' => 'required|max:4000',
            'image' => 'required',
            'start_date' => 'required|date|date-format:Y-m-d',
            'start_date' => 'required|date|date-format:Y-m-d'
        ];
    }

    public function attributes()
    {
        return [
            'position' => '広告掲載位置',
            'link' => '広告のURL',
            'image' => '画像',
            'start_date' => '開始日',
            'start_date' => '終了日'
        ];
    }
}
