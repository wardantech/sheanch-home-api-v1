<?php

namespace App\Http\Controllers\User\Accounts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserExpanseResourse;
use App\Models\Accounts\Transaction;
use App\Traits\ResponseTrait;

class ExpanseController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $expanses = Transaction::where('transaction_purpose', 2)
                ->where('user_id', 1)->get();

            return $this->sendResponse([
                'expanses' => UserExpanseResourse::collection($expanses)
            ], 'Expanses Show Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expanses Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required',
            'cash_out' => 'required',
            'account_id' => 'nullable',
            'remark' => 'nullable|string',
            'user_id' => 'required|integer',
            'mobile_banking_id' => 'nullable',
            'property_id' => 'required|integer',
            'payment_method' => 'required|integer',
            'expanse_item_id' => 'required|integer',
            'property_deed_id' => 'required|integer',
        ]);

        try {
            if($data['payment_method'] === 2) {
                $data['account_id'] = 1;
                unset($data['mobile_banking_id']);
            } elseif ($data['payment_method'] === 3) {
                $data['mobile_banking_id'] = 1;
                unset($data['account_id']);
            }else {
                unset($data['account_id']);
                unset($data['mobile_banking_id']);
            }

            $data['transaction_purpose'] = 2;
            $expanse = Transaction::create($data);

            return $this->sendResponse([
                'expanse' => new UserExpanseResourse($expanse),
            ], 'Expanse Store Successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Expanse Store Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request,Transaction $transaction)
    {
        $data = $request->validate([
            'date' => 'required',
            'cash_out' => 'required',
            'account_id' => 'nullable',
            'remark' => 'nullable|string',
            'user_id' => 'required|integer',
            'mobile_banking_id' => 'nullable',
            'property_id' => 'required|integer',
            'payment_method' => 'required|integer',
            'expanse_item_id' => 'required|integer',
            'property_deed_id' => 'required|integer',
        ]);

        try {
            if($data['payment_method'] === 2) {
                $data['account_id'] = 1;
                unset($data['mobile_banking_id']);
            } elseif ($data['payment_method'] === 3) {
                $data['mobile_banking_id'] = 1;
                unset($data['account_id']);
            }else {
                unset($data['account_id']);
                unset($data['mobile_banking_id']);
            }

            $data['transaction_purpose'] = 2;
            $transaction->update($data);

            return $this->sendResponse([
                'expanse' => new UserExpanseResourse($transaction),
            ], 'Expanse Updated Successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Expanse Update Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response('', 204);
    }
}
