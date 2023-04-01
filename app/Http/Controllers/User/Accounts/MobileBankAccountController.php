<?php

namespace App\Http\Controllers\User\Accounts;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Accounts\Transaction;
use App\Models\Accounts\MobileBanking;
use App\Models\Accounts\AddPaymentMethod;
use App\Models\Accounts\MobileBankAccount;

class MobileBankAccountController extends Controller
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

        $query = MobileBankAccount::with('bank')
            ->whereNotNull('mobile_banking_id')
            ->where('user_id', $userId)
            ->orderBy($columns[$column], $dir);

        $count = MobileBankAccount::count();

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

    public function getMobileBanks()
    {
        $banks = MobileBanking::all();

        return $this->sendResponse([
            'banks' => $banks
        ], 'Bank Show Successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_number' => 'required|string',
            'mobile_banking_id' => 'required|integer',
            'user_id' => 'required|integer',
            'cash_in' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $userId = auth('api')->user()->id;
            $account = MobileBankAccount::create($data);

            $transaction = Transaction::create([
                'user_id' => $data['user_id'],
                'cash_in' => $data['cash_in'],
                'mobile_bank_account_id' => $account->id,
                'payment_method' => 3,
                'transaction_purpose' => 1,
                'created_by' => $userId,
                'is_initial' => 1,
                'date' => now()
            ]);

            DB::commit();
            return $this->sendResponse([
                'accoutn' => $account,
            ], 'Moblie Bank Account Successfully');
        }catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Moblie Bank Account Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function edit(Request $request)
    {
        try {
            $account = MobileBankAccount::findOrFail($request->id);
            $banks = MobileBanking::all();

            $initialBalance = Transaction::where('user_id', $account->user_id)
                ->where('mobile_bank_account_id', $account->id)
                ->where('is_initial', 1)
                ->select('id', 'cash_in')
                ->first();

            return $this->sendResponse([
                'banks' => $banks,
                'account' => $account,
                'initialBalance' => $initialBalance
            ], 'Get Mobile Bank Account Edit Data');

        } catch (\Exception $exception) {
            return $this->sendError('Add Payment Method Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'account_number' => 'required|string',
            'mobile_banking_id' => 'required|integer',
            'user_id' => 'required|integer',
            'cash_in' => 'required',
        ]);

        try {
            $userId = auth('api')->user()->id;
            $account = MobileBankAccount::findOrFail($id);
            $account->update($data);

            $transaction = Transaction::where('user_id', $account->user_id)
                    ->where('mobile_bank_account_id', $account->id)
                    ->where('is_initial', 1)
                    ->first();

            $transaction->update([
                'cash_in' => $data['cash_in'],
                'updated_by' => $userId
            ]);

            return $this->sendResponse([
                'account' => $account,
            ], 'Mobile Bank Account Successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Mobile Bank Account Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $account = MobileBankAccount::findOrFail($id);
        Transaction::where('user_id', $account->user_id)
                    ->where('mobile_bank_account_id', $account->id)
                    ->where('is_initial', 1)
                    ->first()->delete();
        $account->delete();
        return response('', 204);
    }
}
