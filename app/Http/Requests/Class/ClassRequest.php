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
        if (blank($this->title_en) && filled($this->title_ar)) {
            $this->merge(['title_en' => (string) $this->title_ar]);
        }
        if (blank($this->title_ar) && filled($this->title_en)) {
            $this->merge(['title_ar' => (string) $this->title_en]);
        }
        if (blank($this->date)) {
            $this->merge(['date' => date('Y-m-d')]);
        }
        if (blank($this->start_time)) {
            $this->merge(['start_time' => '10:00']);
        }
        if (blank($this->end_time)) {
            $this->merge(['end_time' => '11:00']);
        }

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
            'start_time' => 'required',
            'end_time' => 'required',
            'training_id' => 'required|exists:trainings,id',
            'outcomes.en.*' => 'nullable|string',
            'outcomes.ar.*' => 'nullable|string',
            'bring_with_me.en.*' => 'nullable|string',
            'bring_with_me.ar.*' => 'nullable|string',
        ];
    }
}
