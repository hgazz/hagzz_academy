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
            'price' =>'required|numeric|min:1',
            'name' =>'required|string',
            'country_code' =>'required|regex:/^\+?[0-9]+$/',
            'phone' =>'required|string|unique:users,phone',
            'gender' =>'required|in:male,female',
            'country_id' =>'required|string|exists:countries,id',
            'city_id' =>'required|string|exists:cities,id',
            'area_id' =>'required|string|exists:areas,id',
            'birth_date' =>'required|date',
            'email' => 'required|email|unique:users,email',
            'child_type' => 'required|in:parent,child,athlete',
            'school_name' => 'required|string',
            'parent_name' => 'required|string',
            'parent_phone' => 'required|string|min:7|max:15',
            'coach_preference' => 'required|in:male,female,not_important',
            'frequent_attendance' => 'required|in:daily,weekly,monthly',
            'relation_with_child' => 'required|in:father,mother,brother,sister,guardian',
            'referral_source' => 'required|in:friends,facebook,hagzz_app',
            'medical_condition' => 'required|in:yes,no',
            'medical_condition_details' => 'required_if:medical_condition,yes',
            'additional_information' => 'nullable'
        ];
    }
}
