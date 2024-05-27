<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255',
            'owner_name' => 'required|string|min:3|max:255',
            'logo'=>'nullable|image|mimes:jpg,webp,svg,jpeg,png,webp',
            'phone'=>'required|string|min:7',
            'email'=>'required|email|string',
            'facebook'=>'nullable|string',
            'instagram'=>'nullable|string',
        ];
    }
}
