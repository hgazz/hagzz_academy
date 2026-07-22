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
        return [
            'academy_student_id' => [
                'required',
                Rule::exists('academy_students', 'id')->where(
                    fn ($query) => $query->where('academy_id', auth('academy')->id())
                ),
            ],
            'training_id' => [
                'required',
                Rule::exists('trainings', 'id')->where(
                    fn ($query) => $query->where('academy_id', auth('academy')->id())
                ),
            ],
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,instapay,fawry,app_online,other',
            'payment_method_other' => 'required_if:payment_method,other|nullable|string|max:255',

        ];
    }
}
