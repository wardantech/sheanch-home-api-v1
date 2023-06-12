<?php

namespace App\Http\Controllers\User\Accounts;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Accounts\Transaction;

class TransactionReportController extends Controller
{
    use ResponseTrait;

    public function cash(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $userId = $request['params']['userId'];
        $YearMonth = $request['params']['year_month'];

        if ($YearMonth) {
            $YearMonth = explode("-", $YearMonth);
            $year = $YearMonth[0];
            $month = $YearMonth[1];

            $query = $this->transactions()->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('user_id', $userId)
                ->orderBy($columns[$column], $dir);
        }else {
            $query = $this->transactions()->where('user_id', $userId)
                ->orderBy($columns[$column], $dir);
        }

        $totalRevenue = $query->sum('cash_in');
        $totalExpanse = $query->sum('cash_out');
        $currentAmount = ($totalRevenue - $totalExpanse);

        $count = Transaction::count();

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        if ($length != 'all') {
            $fetchData = $query->paginate($length);
        } else {
            $fetchData = $query->paginate($count);
        }

        return [
            'data' => $fetchData,
            'draw' => $request['params']['draw'],
            'totalRevenue' => $totalRevenue,
            'totalExpanse' => $totalExpanse,
            'currentAmount' => $currentAmount
        ];
    }

    public function revenues(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $userId = $request['params']['userId'];
        $YearMonth = $request['params']['month'];

        if ($YearMonth) {
            $YearMonth = explode("-", $YearMonth);
            $year = $YearMonth[0];
            $month = $YearMonth[1];

            $query = $this->transactions()->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('user_id', $userId)
                ->where('transaction_purpose', 1)
                ->orderBy($columns[$column], $dir);
        }else {
            $query = $this->transactions()->where('user_id', $userId)
                ->where('transaction_purpose', 1)
                ->orderBy($columns[$column], $dir);
        }

        $totalRevenue = $query->sum('cash_in');

        $count = Transaction::count();

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        if ($length != 'all') {
            $fetchData = $query->paginate($length);
        } else {
            $fetchData = $query->paginate($count);
        }

        return [
            'data' => $fetchData,
            'draw' => $request['params']['draw'],
            'totalRevenue' => $totalRevenue
        ];
    }

    public function propertyTransactions(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $userId = $request['params']['userId'];
        $YearMonth = $request['params']['year_month'];
        $propertyId = $request['params']['propertyId'];

        if ($YearMonth) {
            $YearMonth = explode("-", $YearMonth);
            $year = $YearMonth[0];
            $month = $YearMonth[1];

            $query = $this->transactions()->where('property_id', $propertyId)
                ->where('user_id', $userId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->orderBy($columns[$column], $dir);
        }else {
            $query = $this->transactions()->where('property_id', $propertyId)
                ->where('user_id', $userId)
                ->orderBy($columns[$column], $dir);
        }

        $totalRevenue = $query->sum('cash_in');
        $totalExpanse = $query->sum('cash_out');
        $currentAmount = ($totalRevenue - $totalExpanse);

        $count = Transaction::count();

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        if ($length != 'all') {
            $fetchData = $query->paginate($length);
        } else {
            $fetchData = $query->paginate($count);
        }

        return [
            'data' => $fetchData,
            'draw' => $request['params']['draw'],
            'totalRevenue' => $totalRevenue,
            'totalExpanse' => $totalExpanse,
            'currentAmount' => $currentAmount
        ];
    }

    public function bankTransactions(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $userId = $request['params']['userId'];
        $YearMonth = $request['params']['year_month'];
        $bankAccountId = $request['params']['bankAccountId'];

        if ($YearMonth) {
            $YearMonth = explode("-", $YearMonth);
            $year = $YearMonth[0];
            $month = $YearMonth[1];

            $query = $this->transactions()->where('bank_account_id', $bankAccountId)
                ->where('user_id', $userId)->whereYear('date', $year)
                ->whereMonth('date', $month)->orderBy($columns[$column], $dir);
        }else {
            $query = $this->transactions()->where('bank_account_id', $bankAccountId)
                ->where('user_id', $userId)
                ->orderBy($columns[$column], $dir);
        }

        $totalRevenue = $query->sum('cash_in');
        $totalExpanse = $query->sum('cash_out');
        $currentAmount = ($totalRevenue - $totalExpanse);

        $count = Transaction::count();

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        if ($length != 'all') {
            $fetchData = $query->paginate($length);
        } else {
            $fetchData = $query->paginate($count);
        }

        return [
            'data' => $fetchData,
            'draw' => $request['params']['draw'],
            'totalRevenue' => $totalRevenue,
            'totalExpanse' => $totalExpanse,
            'currentAmount' => $currentAmount
        ];
    }

    public function mobileBankTransactions(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $userId = $request['params']['userId'];
        $YearMonth = $request['params']['year_month'];
        $mobileBankId = $request['params']['mobileBankId'];

        if ($YearMonth) {
            $YearMonth = explode("-", $YearMonth);
            $year = $YearMonth[0];
            $month = $YearMonth[1];

            $query = $this->transactions()
                ->where('mobile_bank_account_id', $mobileBankId)
                ->where('user_id', $userId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->orderBy($columns[$column], $dir);
        }else {
            $query = $this->transactions()
                ->where('mobile_bank_account_id', $mobileBankId)
                ->where('user_id', $userId)
                ->orderBy($columns[$column], $dir);
        }

        $totalRevenue = $query->sum('cash_in');
        $totalExpanse = $query->sum('cash_out');
        $currentAmount = ($totalRevenue - $totalExpanse);

        $count = Transaction::count();

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        if ($length != 'all') {
            $fetchData = $query->paginate($length);
        } else {
            $fetchData = $query->paginate($count);
        }

        return [
            'data' => $fetchData,
            'draw' => $request['params']['draw'],
            'totalRevenue' => $totalRevenue,
            'totalExpanse' => $totalExpanse,
            'currentAmount' => $currentAmount
        ];
    }

    private function transactions()
    {
        $transactions = Transaction::with(['bankAccount' => function($query) {
            $query->select('id', 'account_number', 'bank_id')
                ->with(['bank' => function($query) {
                    $query->select('id', 'name');
                }]);
        },
        'mobileBank' => function($query) {
            $query->select('id', 'name');
        }]);

        return $transactions;
    }
}
