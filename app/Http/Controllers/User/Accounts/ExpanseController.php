<?php

namespace App\Http\Controllers\User\Accounts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpanseRequest;
use App\Http\Resources\UserExpanseResourse;
use App\Models\Accounts\ExpanseItem;
use App\Models\Accounts\Transaction;
use App\Models\Property\Property;
use App\Traits\ResponseTrait;

class ExpanseController extends Controller
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

        $query = Transaction::with(['property', 'mobileBank'])
            ->where('user_id', $userId)
            ->where('transaction_purpose', 2);

        $totalExpanse = $query->sum('cash_out');

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
            'totalExpanse' => $totalExpanse
        ];
    }

    public function create(Request $request)
    {
        try {
            $properties = Property::select('id', 'name')
                ->where('user_id', $request->userId)
                ->get();

            $expanseItems = ExpanseItem::select('id', 'name')
                ->where('created_by', $request->userId)
                ->get();

            return $this->sendResponse([
                'properties' => $properties,
                'expanseItems' => $expanseItems
            ], '');
        }catch (\Exception $exception) {
            return $this->sendError('Expanse Create Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function store(StoreExpanseRequest $request)
    {
        $data = $request->validated();

        try {
            if($data['payment_method'] == 2) {
                $data['bank_id'] = $request->bank_id;
                unset($data['mobile_banking_id']);
            } elseif ($data['payment_method'] == 3) {
                $data['mobile_banking_id'] = $request->mobile_banking_id;
                unset($data['bank_id']);
            }else {
                unset($data['bank_id']);
                unset($data['mobile_banking_id']);
            }

            $data['transaction_purpose'] = 2;
            $expanse = Transaction::create($data);

            return $this->sendResponse([
                'expanse' => $expanse
            ], 'Expanse Store Successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Expanse Store Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function edit(Request $request)
    {
        try {
            $properties = Property::select('id', 'name')
                ->where('user_id', $request->userId)
                ->get();

            $expanseItems = ExpanseItem::select('id', 'name')
                ->where('created_by', $request->userId)
                ->get();

            $transaction = Transaction::findOrFail($request->id);

            return $this->sendResponse([
                'properties' => $properties,
                'transaction' => $transaction,
                'expanseItems' => $expanseItems
            ], 'Succssfully get expance item');

        } catch (\Exception $exception) {
            return $this->sendError('Add Payment Method Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(StoreExpanseRequest $request, $id)
    {
        $data = $request->validated();

        try {
            if($data['payment_method'] == 2) {
                $data['bank_id'] = $request->bank_id;
                unset($data['mobile_banking_id']);
            } elseif ($data['payment_method'] == 3) {
                $data['mobile_banking_id'] = $request->mobile_banking_id;
                unset($data['bank_id']);
            }else {
                unset($data['bank_id']);
                unset($data['mobile_banking_id']);
            }

            $transaction = Transaction::findOrFail($request->id);
            $transaction->update($data);

            return $this->sendResponse([
                'transaction' => $transaction
            ], 'Expanse successfully updated');

        }catch (\Exception $exception) {
            return $this->sendError('Expanse Update Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return $this->sendResponse('', 'Expanse delete successfully');
    }
}
