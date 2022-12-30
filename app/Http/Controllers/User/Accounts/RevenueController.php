<?php

namespace App\Http\Controllers\User\Accounts;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Rules\BeforeMonthRule;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\Accounts\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserRevenueResource;
use App\Rules\UniqueDeedDateRule;

class RevenueController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $revenues = Transaction::where('transaction_purpose', 1)
                ->where('user_id', 1)->get();

            return $this->sendResponse([
                'revenues' => UserRevenueResource::collection($revenues)
            ], 'Revenues Show Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Revenues Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        return $request->all();

        $data = $request->validate([
            'cash_in' => 'required',
            'account_id' => 'nullable',
            'remark' => 'nullable|string',
            'user_id' => 'required|integer',
            'mobile_banking_id' => 'nullable',
            'property_id' => 'required|integer',
            'payment_method' => 'required|integer',
            'property_deed_id' => 'required|integer',
            'date' => [
                'required',
                new BeforeMonthRule,
                new UniqueDeedDateRule($request->property_deed_id, $request->user_id)
            ]
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

            $data['transaction_purpose'] = 1;
            $revenues = Transaction::create($data);

            return $this->sendResponse([
                'revenue' => new UserRevenueResource($revenues),
            ], 'Revenue Store Successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Revenue Store Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request,Transaction $transaction)
    {
        $data = $request->validate([
            'cash_in' => 'required',
            'account_id' => 'nullable',
            'remark' => 'nullable|string',
            'user_id' => 'required|integer',
            'mobile_banking_id' => 'nullable',
            'property_id' => 'required|integer',
            'payment_method' => 'required|integer',
            'property_deed_id' => 'required|integer',
            'date' => [
                'required',
                new BeforeMonthRule,
                new UniqueDeedDateRule($request->property_deed_id, $request->user_id)
            ]
        ]);

        return 1;

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

            $data['transaction_purpose'] = 1;
            $transaction->update($data);

            return $this->sendResponse([
                'revenue' => new UserRevenueResource($transaction),
            ], 'Revenue Updated Successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Revenue Updated Error', [
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
