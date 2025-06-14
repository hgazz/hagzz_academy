<?php

namespace App\Http\Requests\Training;

use App\Models\Training;
use App\Rules\checkDiscountValue;
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return[
            'name_en' => 'required|string|max:255|regex:/^[a-zA-Z\s 0-9]*$/',
            'name_ar' => 'required|string|max:255',
            'description_en' => 'required|string|max:255',
            'description_ar' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'coach_id'=>'required|integer|exists:coaches,id',
            'price'=> 'required|integer|min:1',
            'gender' => 'required|in:All,Men,Women',
            'level' => 'required|in:Beginner,Intermediate,Advanced,Any_Level',
            'age_group' => 'required|in:All,Kids,Juniors,Adults',
            'address_id' => 'required|exists:addresses,id',
            'max_players' => 'required|integer',
            'sport_id' => 'required|exists:sports,id',
            'classes_days' => 'required',
            'discount_price' => ['required','integer','min:0', new checkDiscountValue()],
        ];

    }
}
