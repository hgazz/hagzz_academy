<?php

namespace App\Http\Requests\Coach;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]*$/',
            'phone' => 'required|string|max:20',
            'description' => 'required|string',
            'image' => $this->checkImage(),
            'license' => 'nullable|string',
            'license_type' => 'nullable|string',
            'birth_date' => 'date',
            'gender' => 'required|in:male,female',
        ];
    }

    protected function checkImage()
    {
        return request()->isMethod('PUT') ? 'nullable|image|mimes:jpeg,jpg,svg|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
    }
}
