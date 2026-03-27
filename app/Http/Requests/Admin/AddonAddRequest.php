<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array name
 * @property string|int store_id
 * @property string|float price
 * @property array lang
 */
class AddonAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name.*' => 'max:191',
            'name'=>'array|required',
            'store_id' => 'required',
            'price' => 'required|numeric|between:0,999999999999.99',
            'name.0'=>'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('messages.Name is required!'),
            'store_id.required' => translate('messages.please_select_store'),
            'name.0.required'=>translate('default_data_is_required'),
        ];
    }
}
