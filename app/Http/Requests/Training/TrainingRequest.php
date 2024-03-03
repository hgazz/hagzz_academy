<?php

namespace App\Http\Requests\Training;

use App\Models\Training;
use App\Services\TranslatableService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TrainingRequest extends FormRequest
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
            'start_date'=>'required|date|after_or_equal:'. now()->toDateString(),
            'end_date'=>'required|date|after_or_equal:'. now()->toDateString(),
            'coach_id'=>'required|integer|exists:coaches,id',
            'image'=> $this->validateImage(),
            'price'=> 'required|integer'
        ];

        return TranslatableService::validateTranslatableFields(Training::$translatableColumns) + $rules;
    }

    private function validateImage()
    {
        return request()->isMethod('POST') ? 'required|image|mimes:jpg,png,gif,webp,svg,jpeg' : 'nullable|image|mimes:jpg,png,gif,webp,svg,jpeg';
    }
}
