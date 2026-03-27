<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array name
 * @property array lang
 */
class AttributeUpdateRequest extends FormRequest
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
            'name' => 'required|max:100|unique:attributes,name,'.$this->id,
            'name.0' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('messages.Name is required!'),
            'name.0.required'=>translate('default_data_is_required'),
        ];
    }
}
