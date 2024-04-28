<?php

namespace App\Http\Requests\Address;

use App\Models\Address;
use App\Services\TranslatableService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'city_id'=>'required|exists:cities,id',
            'area_id'=>'required|exists:areas,id',
            'longitude'=>'required|string',
            'latitude'=>'required|string',
            'address_en' => 'required|string|regex:/(^([a-zA-Z]+)(\d+)?$)/u',
            'address_ar' => 'required|string|regex:/\p{Arabic}/u',
            'country_id'=>'required|exists:countries,id',
        ];
    }


}
