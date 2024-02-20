<?php

namespace App\Http\Requests\Address;

use App\Models\Address;
use App\Services\TranslatableService;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'academy_id'=>'required|exists:academies,id',
            'city_id'=>'required|exists:cities,id',
            'area_id'=>'required|exists:areas,id',
            'longitude'=>'nullable|string',
            'latitude'=>'nullable|string',
            'active'=>'required',
        ];
        return  TranslatableService::validateTranslatableFields(Address::$translatableColumns) + $rules;
    }
}
