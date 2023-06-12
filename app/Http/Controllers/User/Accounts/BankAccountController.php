<?php

namespace App\Http\Controllers\User\Accounts;

use Illuminate\Http\Request;
use App\Models\Accounts\Bank;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Accounts\BankAccount;
use App\Models\Accounts\Transaction;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $userId = $request['params']['userId'];

        $query = BankAccount::with('bank')
            ->where('user_id', $userId)
            ->orderBy($columns[$column], $dir);

        $count = BankAccount::count();

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

        return ['data' => $fetchData, 'draw' => $request['params']['draw']];
    }

    public function getBanks()
    {
        $banks = Bank::all();

        return $this->sendResponse([
            'banks' => $banks
        ], 'Bank Show Successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_number' => 'required|string|unique:bank_accounts',
            'cash_in' => 'required',
            'bank_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            $userId = auth('api')->user()->id;
            $account = BankAccount::create($data);

            $transaction = Transaction::create([
                'user_id' => $data['user_id'],
                'cash_in' => $data['cash_in'],
                'bank_account_id' => $account->id,
                'payment_method' => 2,
                'transaction_purpose' => 1,
                'created_by' => $userId,
                'is_initial' => 1,
                'date' => now()
            ]);

            DB::commit();
            return $this->sendResponse([
                'bank_account' => $account,
            ], 'Bank Account Create Successfully');
        }catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Bank Account Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function edit(Request $request)
    {
        try {
            $account = BankAccount::findOrFail($request->id);
            $banks = Bank::select('id', 'name')->get();

            $initialBalance = Transaction::where('user_id', $account->user_id)
                ->where('bank_account_id', $account->id)
                ->where('is_initial', 1)
                ->select('id', 'cash_in')
                ->first();

            return $this->sendResponse([
                'banks' => $banks,
                'account' => $account,
                'initialBalance' => $initialBalance
            ], 'Get Bank Account Edit Data');

        } catch (\Exception $exception) {
            return $this->sendError('Add Bank Account Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'account_number' => 'required|string|unique:bank_accounts,account_number,' . $id,
            'cash_in' => 'required',
            'bank_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        DB::beginTransaction();
        try {
            $userId = auth('api')->user()->id;
            $account = BankAccount::findOrFail($id);
            $account->update($data);

            $transaction = Transaction::where('user_id', $account->user_id)
                    ->where('bank_account_id', $account->id)
                    ->where('is_initial', 1)
                    ->first();

            $transaction->update([
                'cash_in' => $data['cash_in'],
                'updated_by' => $userId
            ]);

            DB::commit();
            return $this->sendResponse([
                'account' => $account,
            ], 'Bank Account Updated Successfully');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Bank Account Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $account = BankAccount::findOrFail($id);
        Transaction::where('user_id', $account->user_id)
                    ->where('bank_account_id', $account->id)
                    ->where('is_initial', 1)
                    ->first()->delete();
        $account->delete();
        return response('', 204);
    }
}
