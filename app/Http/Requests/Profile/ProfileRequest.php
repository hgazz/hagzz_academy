<?php

namespace App\Http\Requests\Profile;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|min:3|max:255',
            'owner_name' => 'nullable|string|min:3|max:255',
            'logo'=>'nullable|image|mimes:jpg,webp,svg,jpeg',
            'phone'=>'nullable|string|min:7',
            'email'=>'nullable|email|string',
            'facebook'=>'nullable|string',
            'instagram'=>'nullable|string',
        ];
    }
}
