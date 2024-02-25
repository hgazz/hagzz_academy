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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'city_id'=>'required|exists:cities,id',
            'area_id'=>'required|exists:areas,id',
            'longitude'=>'nullable|string',
            'latitude'=>'nullable|string',
//            'active'=>'nullable|boolean',
        ];
        return  TranslatableService::validateTranslatableFields(Address::$translatableColumns) + $rules;
    }


}
