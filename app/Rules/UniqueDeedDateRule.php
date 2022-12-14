<?php

namespace App\Rules;

use App\Models\Accounts\Transaction;
use Illuminate\Contracts\Validation\Rule;

class UniqueDeedDateRule implements Rule
{
    public int $deedId;
    public int $userId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $deed_id, int $user_id)
    {
        $this->deedId = $deed_id;
        $this->userId = $user_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $transactions = Transaction::where('user_id', $this->userId)
            ->where('property_deed_id', $this->deedId)
            ->get()->toArray();

        foreach($transactions as $transaction) {
            $dbYearDate = date("Y-m", strtotime($transaction['date']));
            $requestDate = date("Y-m", strtotime($value));

            if ($dbYearDate === $requestDate) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You have already taken on this property\'s rent.';
    }
}
