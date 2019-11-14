<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PolicyFormRequest extends FormRequest
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
            'client'            => 'required',
            'cover_number'      => 'required',
            'premium'            => 'required',
            'duration'          => 'required',
            'type'              => 'required',
            'coverage'          => 'required',
            'registration_date' => 'required',
        ];
    }
}
