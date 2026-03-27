<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property array title
 * @property string code
 * @property Carbon|null start_date
 * @property Carbon|null expire_date
 * @property float min_purchase
 * @property float max_discount
 * @property float discount
 * @property string discount_type
 * @property string coupon_type
 * @property int|null limit
 * @property bool status
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property string|null data
 * @property int total_uses
 * @property int module_id
 * @property string created_by
 * @property string customer_id
 * @property string|null slug
 * @property int|null store_id
 * @property array lang
 */
class CouponAddRequest extends FormRequest
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
            'code' => 'required|unique:coupons|max:100',
            'title' => 'required|max:191',
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required',
            'coupon_type' => 'required|in:zone_wise,store_wise,free_delivery,first_order,default',
            'zone_ids' => 'required_if:coupon_type,zone_wise',
            'store_ids' => 'required_if:coupon_type,store_wise',
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
