<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

/**
 * @property array name
 * @property array lang
 * @property int id
 * @property string|null slug
 * @property bool status
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */
class BrandAddRequest extends FormRequest
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
            'name' => 'required|unique:brands|max:100',
            'name.0' => 'required',
            'image' => 'required',
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
