<?php

namespace App\Http\Controllers\User\Property;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRentCollectionRequest;
use App\Http\Requests\UpdateRentCollectionRequest;
use App\Http\Resources\BankAccountResource;
use App\Http\Resources\MobileBankAccountResource;
use App\Models\Accounts\Transaction;
use App\Models\Property\PropertyDeed;
use App\Http\Resources\UserRevenueResource;
use App\Models\Accounts\AddPaymentMethod;
use App\Models\Accounts\BankAccount;
use App\Models\Accounts\MobileBankAccount;
use App\Models\Property\Property;
use App\Service\TransactionsService;
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

        $query = DB::table("transactions")
            ->where('transactions.user_id', $userId)
            ->whereNull('transactions.deleted_at')
            ->join("properties", 'transactions.property_id', '=', 'properties.id')
            ->join("property_deeds", 'transactions.property_deed_id', '=', 'property_deeds.id')
            ->join("users", "users.id", '=', 'property_deeds.tenant_id')
            ->select([
                "properties.name as property_name",
                "properties.total_amount as property_amount",
                "users.name as tenant_name",
                "property_deeds.id as deedId",
                DB::raw("MONTH(transactions.date) as month"),
                DB::raw("MONTHNAME(transactions.date) as monthName"),
                DB::raw("YEAR(transactions.date) as year"),
                DB::raw("SUM(transactions.cash_in) as amount")
            ])
            ->groupBy(['property_name','tenant_name','month', 'monthName', 'year', 'property_amount', 'deedId']);

            // ->orderBy('year', 'desc');

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
            'draw' => $request['params']['draw']
        ];
    }

    public function getDeedTransactionMonth(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $deedId = $request['params']['deedId'];
        $month = $request['params']['month'];

        $query = Transaction::with([
            'property', 'deed' => function($query) {
            $query->with('tenant');
        }, 'mobileBank' => function($query) {
            $query->select('id', 'name');
        }, 'bankAccount' => function($query) {
            $query->select('id', 'bank_id', 'account_number')
                ->with(['bank' => function($query) {
                    $query->select('id', 'name');
                }]);
        }])
        ->where('property_deed_id', $deedId)
        ->whereMonth('date', $month);

        $payAmount = $query->sum('cash_in');

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
            'pay_amount' => $payAmount,
            'data' => $fetchData,
            'draw' => $request['params']['draw']
        ];
    }

    // For another loginc;
    public function getRentDeed(Request $request)
    {
        try {
            $deeds = PropertyDeed::select('id', 'property_id', 'landlord_id', 'tenant_id')
                ->with(['property' => function($property) {
                    $property->select('id', 'name');
                }, 'landlord' => function($landlord) {
                    $landlord->select('id', 'name');
                }, 'tenant' => function($tenant) {
                    $tenant->select('id', 'name');
                }])
                ->where('landlord_id', $request->userId)->where('status', 5)
                ->get();

            return $this->sendResponse([
                'deeds' => $deeds
            ], 'All deed successfully get');
        }catch (\Exception $exception){
            return $this->sendError('Deed Error', ['error' => $exception->getMessage()]);
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

        try {
            if($data['payment_method'] == 2) {
                $data['bank_account_id'] = $request->bank_account_id;
                unset($data['mobile_bank_account_id']);
            } elseif ($data['payment_method'] == 3) {
                $data['mobile_bank_account_id'] = $request->mobile_bank_account_id;
                unset($data['bank_account_id']);
            }else {
                unset($data['bank_account_id']);
                unset($data['mobile_bank_account_id']);
            }

            $data['transaction_purpose'] = 1; // Revenue
            $data['created_by'] = auth('api')->user()->id;
            $revenues = Transaction::create($data);

            return $this->sendResponse([
                'revenue' => new UserRevenueResource($revenues),
            ], 'Payment Successfully Added');

        }catch (\Exception $exception) {
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
        try {
            $deeds = PropertyDeed::with('property', 'landlord', 'tenant')
                ->where('landlord_id', $request->userId)
                ->where('status', 5)
                ->get();

            $transaction = Transaction::findOrFail($request->transactionId);

            return $this->sendResponse([
                'deeds' => $deeds,
                'transaction' => $transaction
            ], 'All deed successfully get');
        }catch (\Exception $exception){
            return $this->sendError('Deed Edit Error', ['error' => $exception->getMessage()]);
        }
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

        try {
            if($data['payment_method'] == 2) {
                $data['transaction_id'] = null;
                $data['mobile_bank_account_id'] = null;
                $data['bank_account_id'] = $request->bank_account_id;
            } elseif ($data['payment_method'] == 3) {
                $data['mobile_bank_account_id'] = $request->mobile_bank_account_id;
                $data['bank_account_id'] = null;
            }else {
                $data['transaction_id'] = null;
                $data['bank_account_id'] = null;
                $data['mobile_bank_account_id'] = null;
            }

            $transaction = Transaction::findOrFail($id);

            $data['transaction_purpose'] = 1;
            $data['updated_by'] = auth('api')->user()->id;
            $transaction->update($data);

            return $this->sendResponse([
                'revenue' => new UserRevenueResource($transaction),
            ], 'Payment Successfully Updated');

        } catch (\Exception $exception) {
            return $this->sendError('Payment Update Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function due(Request $request)
    {
        try {
            $transaction = Transaction::with('property')
                ->findOrFail($request->transactionId);

            $payAmount = Transaction::where('property_deed_id', $transaction->property_deed_id)
                        ->whereMonth('date', $transaction->date)->sum('cash_in');

            return $this->sendResponse([
                'transaction' => $transaction,
                'pay_amount' => $payAmount
            ], 'Payment Successfully Updated');
        }catch (\Exception $exception){
            return $this->sendError('Deed Edit Error', ['error' => $exception->getMessage()]);
        }
    }

    public function dueStore(Request $request)
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
            'transaction_id' => 'nullable',
            'date' => 'required'
        ]);

        try {
            if($data['payment_method'] == 2) {
                $data['bank_account_id'] = $request->bank_account_id;
                unset($data['mobile_bank_account_id']);
            } elseif ($data['payment_method'] == 3) {
                $data['mobile_bank_account_id'] = $request->mobile_bank_account_id;
                unset($data['bank_account_id']);
            }else {
                unset($data['bank_account_id']);
                unset($data['mobile_bank_account_id']);
            }

            $data['transaction_purpose'] = 1; // Due
            $revenues = Transaction::create($data);

            return $this->sendResponse([
                'revenue' => new UserRevenueResource($revenues),
            ], 'Due Payment Successfully Added');

        }catch (\Exception $exception) {
            return $this->sendError('Payment Store Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Method selection.
     *
     * @param  mixed $request
     * @return void
     */
    public function getAccounts(Request $request)
    {
        try {
            $accounts = $this->accounts($request->method, $request->userId);

            return $this->sendResponse([
                'accounts'=> $accounts,
            ],'Accounts details get successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Accounts get error', [
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
    protected function accounts($method, $userId)
    {
        $accounts = null;
        if ($method == 2) {
            $accounts = BankAccount::with('bank')
                ->whereNotNull('bank_id')
                ->where('user_id', $userId)
                ->get();

            return BankAccountResource::collection($accounts);
        }

        if ($method == 3) {
            $accounts = MobileBankAccount::with('mobileBank')
                ->whereNotNull('mobile_banking_id')
                ->where('user_id', $userId)
                ->get();

            return MobileBankAccountResource::collection($accounts);
        }

        return $accounts;
    }

    public function getPropertyInfo(Request $request)
    {
        try {
            $property = Property::with(['deed' => function($query) {
                $query->where('status', 5);
            }])->where('id', $request->propertyId)->first();

            return $this->sendResponse([
                'property' => $property,
            ], 'Property get successfully');
        }catch (\Exception $exception){
            return $this->sendError('Property error', ['error' => $exception->getMessage()]);
        }
    }
}
