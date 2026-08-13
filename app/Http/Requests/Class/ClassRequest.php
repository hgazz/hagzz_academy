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

    protected function prepareForValidation(): void
    {
        if ($this->start_time && strlen((string)$this->start_time) > 5) {
            $this->merge([
                'start_time' => substr((string)$this->start_time, 0, 5),
            ]);
        }
        if ($this->end_time && strlen((string)$this->end_time) > 5) {
            $this->merge([
                'end_time' => substr((string)$this->end_time, 0, 5),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'date' => ['required', 'date', new ValidateDate()],
            'start_time' => 'required|date_format:H:i',
            'end_time' => [
                'required',
                'date_format:H:i',
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($this->start_time && $value === $this->start_time) {
                        $fail(app()->getLocale() === 'ar'
                            ? 'يجب أن يختلف وقت نهاية الحصة عن وقت البداية.'
                            : 'The session end time must be different from the start time.');
                    }
                },
            ],
            'training_id' => 'required|exists:trainings,id',
            'outcomes.en.*' => 'nullable|string',
            'outcomes.ar.*' => 'nullable|string',
            'bring_with_me.en.*' => 'nullable|string',
            'bring_with_me.ar.*' => 'nullable|string',
        ];
    }
}
