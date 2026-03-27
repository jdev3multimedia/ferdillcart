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
 * @property array title
 * @property string type
 * @property string|null image
 * @property bool status
 * @property string data
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property int zone_id
 * @property int module_id
 * @property bool featured
 * @property string|null default_link
 * @property string created_by
 * @property array lang
 */
class DmVehicleUpdateRequest extends FormRequest
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
            'type' => 'required|max:254|unique:d_m_vehicles,type,'.$this->id,
            'extra_charges' => 'required||numeric|between:0,999999999999.99',
            'starting_coverage_area' => 'required||numeric|between:0,999999999999.99',
            'maximum_coverage_area' => 'required||numeric|between:.01,999999999999.99|gt:starting_coverage_area',
            'type.0' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'type.0.required'=>translate('default_type_is_required'),
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $response = response()->json(['errors' => Helpers::error_processor($validator)]);
        throw new ValidationException($validator, $response);
    }
}
