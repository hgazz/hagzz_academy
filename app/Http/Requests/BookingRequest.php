<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
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
        $user = auth('academy')->user();
        $academyId = $user instanceof \App\Models\PartnerUser ? (int) $user->academy_id : (int) auth('academy')->id();

        return [
            'academy_student_id' => [
                'required',
                Rule::exists('academy_students', 'id')->where(
                    fn ($query) => $query->where('academy_id', $academyId)
                ),
            ],
            'training_id' => [
                'required',
                Rule::exists('trainings', 'id')->where(
                    fn ($query) => $query->where('academy_id', $academyId)
                ),
            ],
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:60',
            'payment_method_other' => 'required_if:payment_method,other|nullable|string|max:255',
        ];
    }
}
