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
 * @property array module_data
 * @property array lang
 */
class ZoneModuleUpdateRequest extends FormRequest
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
            // 'cash_on_delivery' => 'required_without_all:digital_payment,offline_payment',
            // 'digital_payment' => 'required_without_all:cash_on_delivery,offline_payment',
            // 'offline_payment' => 'required_without_all:cash_on_delivery,digital_payment',
            // 'increased_delivery_fee' => 'nullable|numeric|between:0,999.99|required_if:increased_delivery_fee_status,1',
            'module_data' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            // 'increased_delivery_fee.required_if' => translate('messages.increased_delivery_fee_is_required'),
            'module_data.required' => translate('messages.business_module_data_is_required'),
        ];
    }
}
