<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemCreateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'category_id' => 'required|numeric',
            'sub_category_id' => 'required|numeric',
            'business_link' => 'required|string:255',
            'title' => 'required|string:255',
            'description' => 'required|string:255',
            'price' => 'required'
        ];
    }
}
