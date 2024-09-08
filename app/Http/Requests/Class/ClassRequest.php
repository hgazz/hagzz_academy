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
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string',
            'date' =>['required',new ValidateDate()],
            'start_time' => $this->checkStartTime(),
            'end_time' => $this->checkEndTime(),
            'training_id'=>'required|exists:trainings,id',
            'outcomes.en.*' => 'nullable|string',
            'outcomes.ar.*' => 'nullable|string',
            'bring_with_me.en.*' => 'nullable|string',
            'bring_with_me.ar.*' => 'nullable|string',
        ];
    }

    public function checkStartTime()
    {
        return $this->request->isMethod('post') ? 'required|date_format:H:i' : 'required';
    }

    public function checkEndTime()
    {
        return $this->request->isMethod('post') ? 'required|date_format:H:i|after:start_time' : 'required';
    }
}
