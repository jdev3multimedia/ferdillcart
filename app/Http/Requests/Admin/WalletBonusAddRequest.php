<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property array|string title
 * @property array translations
 * @property string|null|array description
 * @property string bonus_type
 * @property float bonus_amount
 * @property float minimum_add_amount
 * @property float maximum_bonus_amount
 * @property Carbon|null start_date
 * @property Carbon|null end_date
 * @property bool status
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property array lang
 */
class WalletBonusAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.     */
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
            'title' => 'required|max:191',
            'start_date' => 'required',
            'end_date' => 'required',
            'bonus_type' => 'required|in:percentage,amount',
            'bonus_amount' => 'required',
            'minimum_add_amount' => 'required',
            'maximum_bonus_amount' => 'required_if:bonus_type,percentage',
            'title.0' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.0.required'=>translate('default_title_is_required'),
        ];
    }
}
