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
        $training =  Training::where('id',request('training_id'))->first();

        if (is_null($training)){
            $fail(trans('admin.clasess.training_not_found'));
        }else{
            $startDate = Carbon::parse($training->start_date);
            $endDate = Carbon::parse($training->end_date);
            $checkDate = Carbon::parse(request('date'));

            if (!$checkDate->between($startDate, $endDate)) {
                $fail(trans("admin.clasess.date_outside_range", [ 'startDate' => $startDate, 'endDate' => $endDate]));
            }
        }
    }

}
