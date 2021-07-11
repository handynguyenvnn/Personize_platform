<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            'title' => 'required',
            'time' => 'nullable|date-format:H:i',
            'date' => 'nullable|date|date-format:Y-m-d',
            'image' => 'required',
            'link_stream' => 'required|max:4000'
        ];
    }

    public function attributes()
    {
        return [
            'title'       => 'タイトル',
            'time'        => '時間',
            'date'        => '日付',
            'image'       => '画像',
            'link_stream' => '配信URL'
        ];
    }
}
