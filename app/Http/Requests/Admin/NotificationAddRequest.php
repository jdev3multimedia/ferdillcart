<?php

namespace App\Http\Requests\Admin;

use App\CentralLogics\Helpers;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * @property int id
 * @property string title
 * @property string description
 * @property string|null image
 * @property bool status
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property string|null tergat
 * @property int|null zone_id
 */
class NotificationAddRequest extends FormRequest
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
            'notification_title' => 'required|max:191',
            'description' => 'required|max:1000',
            'tergat' => 'required',
            'zone'=>'required'
        ];
    }

    public function messages(): array
    {
        return [
            'notification_title.required' => 'Title is required!',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $response = response()->json(['errors' => Helpers::error_processor($validator)]);
        throw new ValidationException($validator, $response);
    }
}
