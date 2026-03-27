<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array name
 * @property array coordinates
 * @property string store_wise_topic
 * @property string customer_wise_topic
 * @property string deliveryman_wise_topic
 * @property string|int cash_on_delivery
 * @property string|int digital_payment
 * @property string|int offline_payment
 * @property array lang
 */
class ZoneUpdateRequest extends FormRequest
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
            'name' => 'required|max:191|unique:zones,name,'.$this->id,
            'coordinates' => 'required',
            'name.0' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.0.required'=>translate('default_name_is_required'),
        ];
    }
}
