<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
        return [
            'training_id' => 'required|exists:trainings,id',
            'price' =>'required|numeric',
            'name' =>'required|string',
            'phone' =>'required|string|unique:users,phone',
            'gender' =>'required|in:male,female',
            'country_code' =>'required',
            'birth_date' =>'required|date|before:today',
            'country_id' =>'required|string|exists:countries,id',
            'city_id' =>'required|string|exists:cities,id',
            'area_id' =>'required|string|exists:areas,id',
        ];
    }
}
