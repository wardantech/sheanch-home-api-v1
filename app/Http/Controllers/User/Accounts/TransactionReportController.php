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

        $query = Transaction::with('mobileBank')
            ->where('user_id', $userId);

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
}
