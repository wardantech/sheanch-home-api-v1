<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class BeforeMonthRule implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $inputDate = strtotime($value);
        $lastday = strtotime('last day of this month');

        if ($inputDate > $lastday) {
            $fail('You can only takes previous month rent.');
        }
    }
}
