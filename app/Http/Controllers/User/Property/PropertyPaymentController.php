<?php

namespace App\Http\Controllers\User\Property;

use App\Models\Accounts\Due;
use Illuminate\Http\Request;
use App\Models\Accounts\Bank;
use App\Rules\BeforeMonthRule;
use App\Rules\UniqueDeedDateRule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Accounts\Transaction;
use App\Models\Property\PropertyDeed;
use App\Models\Accounts\MobileBanking;
use App\Http\Resources\UserRevenueResource;
use App\Traits\ResponseTrait;

class PropertyPaymentController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getRentProperty(Request $request)
    {
        try {
            $propertyDeed = PropertyDeed::with('property', 'tenant')->findOrFail($request->deedId);
            $banks = Bank::all();
            $mobileBanks = MobileBanking::all();

            return $this->sendResponse([
                'deed'=> $propertyDeed,
                'banks' => $banks,
                'mobiles' => $mobileBanks
            ],'Property deed get successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Property Deed status change error', ['error' => $exception->getMessage()]);
        }
    }

    // Store Rent By Deed
    public function RentPropertyStore(Request $request)
    {
        $data = $request->validate([
            'cash_in' => 'required',
            'bank_id' => 'nullable',
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

        DB::beginTransaction();
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

            if ($request->due_amount) {
                $due = new Due();

                $due->user_id = $request->user_id;
                $due->property_id = $request->property_id;
                $due->property_deed_id = $request->property_deed_id;
                $due->amount = $request->due_amount;
                $due->date = $request->date;
                $due->save();

                $data['due_id'] = $due->id;
            }

            $data['transaction_purpose'] = 1;
            $revenues = Transaction::create($data);

            DB::commit();
            return $this->sendResponse([
                'revenue' => new UserRevenueResource($revenues),
            ], 'Payment Successfully Added');

        }catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Payment Store Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function getPropertyPayments(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $userId = $request['params']['userId'];

        $query = Transaction::with('due', 'property')
            ->where('user_id', $userId)
            ->orderBy($columns[$column], $dir);

        $count = PropertyDeed::count();

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

    public function destroyPropertyPayment(Request $request)
    {
        try {
            $transaction = Transaction::findOrFail($request->id);
            if ($transaction->due_id) {
                $transaction->due->delete();
            }
            return $transaction->delete();

            return $this->sendResponse(['id' => $request->id],'Payment deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Payment delete error', ['error' => $exception->getMessage()]);
        }
    }
}
