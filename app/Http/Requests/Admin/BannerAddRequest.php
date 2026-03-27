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
class BannerAddRequest extends FormRequest
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
            'title' => 'required|max:191',
            'image' => 'required',
            'banner_type' => 'required',
            'zone_id' => 'required',
            'store_id' => 'required_if:banner_type,store_wise',
            'item_id' => 'required_if:banner_type,item_wise',
            'title.0' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'zone_id.required' => translate('messages.select_a_zone'),
            'store_id.required_if'=> translate('messages.store is required when banner type is store wise'),
            'item_id.required_if'=> translate('validation.required_if',['attribute'=>translate('messages.item'), 'other'=>translate('messages.banner_type'), 'value'=>translate('messages.item_wise')]),
            'title.0.required'=>translate('default_data_is_required'),
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $response = response()->json(['errors' => Helpers::error_processor($validator)]);
        throw new ValidationException($validator, $response);
    }
}
