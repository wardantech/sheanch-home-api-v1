<?php

namespace App\Http\Controllers\User\Property;

use App\Models\Accounts\Due;
use Illuminate\Http\Request;
use App\Rules\BeforeMonthRule;
use App\Rules\UniqueDeedDateRule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRentCollectionRequest;
use App\Http\Requests\UpdateRentCollectionRequest;
use App\Models\Accounts\Transaction;
use App\Models\Property\PropertyDeed;
use App\Http\Resources\UserRevenueResource;
use App\Models\Accounts\AddPaymentMethod;
use App\Models\Property\Property;
use App\Traits\ResponseTrait;

class RentCollectionController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get all properties payment list.
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
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

    /**
     * Rent collection create page show
     *
     * @param  mixed $request
     * @return void
     */
    public function create(Request $request)
    {
        try {
            $propertyDeed = PropertyDeed::with('property', 'tenant')->findOrFail($request->deedId);

            return $this->sendResponse([
                'deed'=> $propertyDeed
            ],'Property deed get successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Property Deed status change error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Method selection.
     *
     * @param  mixed $request
     * @return void
     */
    public function getPaymentMethod(Request $request)
    {
        try {
            $paymentMethod = $this->paymentMethod($request->method, $request->userId);

            return $this->sendResponse([
                'banks'=> $paymentMethod
            ],'Payment method get successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Property Deed status change error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Store rent collection.
     *
     * @param  mixed $request
     * @return void
     */
    public function store(StoreRentCollectionRequest $request)
    {
        $data = $request->validated();

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

    /**
     * Show rent collection edit page.
     *
     * @param  mixed $request
     * @return void
     */
    public function edit(Request $request)
    {
        $transaction = Transaction::with(['property', 'due', 'deed' => function($query) {
            $query->with('tenant');
        }])
        ->findOrFail($request->transactionId);

        return $this->sendResponse([
            'transaction' => $transaction
        ], 'Transaction Edit Data');
    }

    /**
     * Update rent collection
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(UpdateRentCollectionRequest $request, $id)
    {
        $data = $request->validated();

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

            $transaction = Transaction::findOrFail($id);

            // If Have A Due
            $dueId = $this->dueUpdate($transaction, $request);

            $data['due_id'] = $dueId;
            $data['transaction_purpose'] = 1;
            $transaction->update($data);

            DB::commit();
            return $this->sendResponse([
                'revenue' => new UserRevenueResource($transaction),
            ], 'Payment Successfully Updated');

        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Payment Update Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Destroy rent collection.
     *
     * @param  mixed $request
     * @return void
     */
    public function destroy(Request $request)
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

    /**
     * Payment method selection for spacific user
     *
     * @param  mixed $method
     * @param  mixed $userId
     * @return void
     */
    protected function paymentMethod($method, $userId)
    {
        $paymentMethod = null;
        if ($method == 2) {
            $paymentMethod = AddPaymentMethod::with('bank')
                ->whereNotNull('bank_id')
                ->where('user_id', $userId)
                ->get();
        }

        if ($method == 3) {
            $paymentMethod = AddPaymentMethod::with('mobileBank')
                ->whereNotNull('mobile_banking_id')
                ->where('user_id', $userId)
                ->get();
        }

        return $paymentMethod;
    }

    /**
     * dueUpdate
     *
     * @param  mixed $transaction
     * @param  mixed $request
     * @return integer
     */
    protected function dueUpdate($transaction, $request)
    {
        if ($request->due_amount) {
            if ($transaction->due) {
                $transaction->due->amount = $request->due_amount;
                $transaction->due->date = date("Y-m-d", strtotime($request->date));
                $transaction->due->update();

                return $transaction->due->id;
            }else {
                $due = new Due();

                $due->user_id = $request->user_id;
                $due->property_id = $request->property_id;
                $due->property_deed_id = $request->property_deed_id;
                $due->amount = $request->due_amount;
                $due->date = date("Y-m-d", strtotime($request->date));
                $due->save();

                return $due->id;
            }
        }else {
            if ($transaction->due) {
                $transaction->due->amount = $request->due_amount;
                $transaction->due->date = date("Y-m-d", strtotime($request->date));
                $transaction->due->update();

                return $transaction->due->id;
            }
        }
    }








    // For another loginc;
    public function getRentDeed(Request $request)
    {
        try {
            $deeds = PropertyDeed::with('property', 'tenant')
                ->where('status', 2)
                ->where('landlord_id', $request->userId)->get();

            return $this->sendResponse([
                'deeds' => $deeds,
            ], 'All deed successfully get');
        }catch (\Exception $exception){
            return $this->sendError('Deed Error', ['error' => $exception->getMessage()]);
        }
    }

    public function getPropertyInfo(Request $request)
    {
        try {
            $property = Property::with(['deed' => function($query) {
                $query->where('status', 2);
            }])->where('id', $request->propertyId)->first();

            // return $property->deed[0]->updated_at;

            return $this->sendResponse([
                'property' => $property,
            ], 'Property get successfully');
        }catch (\Exception $exception){
            return $this->sendError('Property error', ['error' => $exception->getMessage()]);
        }
    }
}
