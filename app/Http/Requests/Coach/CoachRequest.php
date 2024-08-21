<?php

namespace App\Http\Requests\Coach;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class CoachRequest extends FormRequest
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
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description_en' => 'required|string|max:255',
            'description_ar' => 'required|string|max:255',
            'license_en' => 'string|nullable',
            'license_ar' => 'string|nullable',
            'license_type_en' => 'string|nullable',
            'license_type_ar' => 'string|nullable',
            'phone' => 'required|string|max:20',
            'image' => $this->checkImage(),
            'birth_date' => 'required|date|before:' . Carbon::now()->subYears(10)->format('Y-m-d'),
            'gender' => 'required|in:male,female',
        ];
    }

    protected function checkImage()
    {
        return request()->isMethod('PUT') ? 'nullable|image|mimes:jpeg,jpg,svg|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
    }
}
