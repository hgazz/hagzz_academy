<?php

namespace App\Rules;

use App\Models\Training;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;
class ValidateDate implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            if (blank($value)) {
                return;
            }

            $trainingId = request('training_id');
            if (!$trainingId) {
                return;
            }

            $training = Training::find($trainingId);
            if (!$training) {
                return;
            }

            if (!empty($training->start_date) && !empty($training->end_date)) {
                $checkDate = Carbon::parse($value)->startOfDay();
                $startDate = Carbon::parse($training->start_date)->startOfDay();
                $endDate = Carbon::parse($training->end_date)->endOfDay();

                if ($checkDate->lt($startDate) || $checkDate->gt($endDate)) {
                    $fail(trans("admin.clasess.date_outside_range", [
                        'startDate' => $startDate->format('Y-m-d'),
                        'endDate' => $endDate->format('Y-m-d')
                    ]));
                }
            }
        } catch (\Throwable $e) {
            // Never crash validation
        }
    }

}
