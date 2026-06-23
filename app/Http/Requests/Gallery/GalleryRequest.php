<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GalleryRequest extends FormRequest
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
            'image' => $this->isMethod('put')
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
