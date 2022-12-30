<?php

namespace App\Http\Controllers\User\Accounts;

use Illuminate\Http\Request;
use App\Models\Accounts\Bank;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Accounts\AddPaymentMethod;
use App\Models\Accounts\MobileBanking;

class AddMobileMethodController extends Controller
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

        $query = AddPaymentMethod::with('mobileBank')
            ->whereNotNull('mobile_banking_id')
            ->where('user_id', $userId)
            ->orderBy($columns[$column], $dir);

        $count = AddPaymentMethod::count();

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
            'account_number' => 'required|string|unique:add_payment_methods',
            'mobile_banking_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        try {
            $paymentMethod = AddPaymentMethod::create($data);

            return $this->sendResponse([
                'paymentMethod' => $paymentMethod,
            ], 'Payment Method Store Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Add Payment Method Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function edit(Request $request)
    {
        try {
            $paymentMethod = AddPaymentMethod::findOrFail($request->id);
            $banks = MobileBanking::all();

            return $this->sendResponse([
                'paymentMethod' => $paymentMethod,
                'banks' => $banks
            ], 'Get Payment Method Edit Data');

        } catch (\Exception $exception) {
            return $this->sendError('Add Payment Method Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'account_number' => 'required|string|unique:add_payment_methods,account_number,' . $id,
            'mobile_banking_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        try {
            $paymentMethod = AddPaymentMethod::findOrFail($id);
            $paymentMethod->update($data);

            return $this->sendResponse([
                'paymentMethod' => $paymentMethod,
            ], 'Payment Method Updated Successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Add Payment Method Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $paymentMethod = AddPaymentMethod::findOrFail($id);
        $paymentMethod->delete();
        return response('', 204);
    }
}
