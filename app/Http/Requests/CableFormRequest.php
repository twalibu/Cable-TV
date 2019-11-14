<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CableFormRequest extends FormRequest
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
            'reference'         => 'required',
            'starting_date'     => 'required',
            'type'              => 'required',
        ];
    }
}
