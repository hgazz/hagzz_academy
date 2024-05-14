<?php

namespace App\Http\Requests\Class;

use App\Models\TClass;
use App\Rules\ValidateDate;
use App\Services\TranslatableService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ClassRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'date' =>['required',new ValidateDate()],
            'training_id'=>'required|exists:trainings,id',
            'outcomes.*' => 'nullable|string',
            'bring_with_me.*' => 'nullable|string',
        ];
    }
}
