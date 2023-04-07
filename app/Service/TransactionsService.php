<?php

namespace App\Service;

use App\Models\Accounts\Transaction;

class TransactionsService
{
    public static function totalAmountInBank($bank_id)
    {
        $totalAmount = 0;
        $cashIn = Transaction::where('bank_account_id', $bank_id)->sum('cash_in');
        $cashOut = Transaction::where('bank_account_id', $bank_id)->sum('cash_out');

        $totalAmount = $cashIn - $cashOut;

        return $totalAmount;
    }

    public static function totalAmountInMobileBank($mobile_id)
    {
        $totalAmount = 0;
        $cashIn = Transaction::where('mobile_bank_account_id', $mobile_id)->sum('cash_in');
        $cashOut = Transaction::where('mobile_bank_account_id', $mobile_id)->sum('cash_out');

        $totalAmount = $cashIn - $cashOut;

        return $totalAmount;
    }
}
