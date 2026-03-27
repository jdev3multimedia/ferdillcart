<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array id
 * @property array unit
 * @property array lang
 */
class UnitUpdateRequest extends FormRequest
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
            'unit'=>'required|unique:units,unit,'.$this->id,
            'unit.0' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'unit.0.required'=>translate('default_unit_is_required'),
        ];
    }
}
