<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

/**
 * @property array lang
 * @property int id
 * @property array module_name
 * @property string module_type
 * @property string|null thumbnail
 * @property bool status
 * @property int stores_count
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property string|null icon
 * @property int theme_id
 * @property array description
 * @property bool all_zone_service
 */
class ModuleAddRequest extends FormRequest
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
            'module_name' => 'required|unique:modules|max:100',
            'module_type'=>'required',
            'icon'=>'required',
            'thumbnail'=>'required',
            'module_name.0' => 'required',
            'description.0' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'module_name.required' => translate('messages.Name is required!'),
            'module_name.0.required'=>translate('default_name_is_required'),
            'description.0.required'=>translate('default_description_is_required'),
        ];
    }
}
