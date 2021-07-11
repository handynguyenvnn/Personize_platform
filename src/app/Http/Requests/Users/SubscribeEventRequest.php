<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscribeEventRequest extends FormRequest
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
            'event_id' => [
                'required',
                Rule::unique('user_event')->where(function ($query) use($request) {
                    $query->where('event_id', $request->event_id)
                        ->where('user_id', auth()->user()->id)
                        ->whereNull('deleted_at');
                }),
            ]
        ];
    }
}
