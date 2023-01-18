<?php

namespace App\Http\Controllers\User\Property;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Property\Property;
use App\Service\DateTimesService;
use App\Http\Controllers\Controller;
use App\Models\Accounts\Transaction;
use App\Models\Property\PropertyDeed;

class DueCollectionController extends Controller
{
    use ResponseTrait;

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

        $query = Transaction::with('property')
            ->where('transaction_purpose', 3) // Only Due Collection Show
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

    // For another loginc;
    public function getDueDeed(Request $request)
    {
        try {
            $deeds = PropertyDeed::with('property', 'landlord', 'tenant')
                ->where('landlord_id', $request->userId)
                ->where('status', 5)
                ->get();

            return $this->sendResponse([
                'deeds' => $deeds
            ], 'All deed successfully get');
        }catch (\Exception $exception){
            return $this->sendError('Deed Error', ['error' => $exception->getMessage()]);
        }
    }

    public function getDueAmount(Request $request)
    {
        try {
            $property = Property::with(['deed' => function($query) {
                $query->with(['transactions' => function($query) {
                    $query->select('id', 'property_deed_id', 'cash_in');
                }])->where('status', 5);
            }])->where('id', $request->propertyId)->first();

            $startDate = $property->deed[0]->start_date;
            $currentMonth = date('Y-m-d');

            $months = DateTimesService::monthBetweenDate($startDate, $currentMonth) + 1;

            $payAmount = $property->deed[0]->transactions->sum('cash_in');
            $totalRent = $months * $property->total_amount;
            $totalDue = $totalRent - $payAmount;

            return $this->sendResponse([
                'property' => $property,
                'totalDue' => $totalDue
            ], 'Property get successfully');
        } catch (\Exception $exception){
            return $this->sendError('Property error', ['error' => $exception->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cash_in' => 'required',
            'due_amount' => 'nullable',
            'bank_id' => 'nullable',
            'remark' => 'nullable|string',
            'user_id' => 'required|integer',
            'mobile_banking_id' => 'nullable',
            'property_id' => 'required|integer',
            'payment_method' => 'required|integer',
            'property_deed_id' => 'required|integer',
            'transaction_id' => 'nullable|string',
            'date' => 'required'
        ]);

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

            $data['transaction_purpose'] = 3; // Due
            $due = Transaction::create($data);

            return $this->sendResponse([
                'due' => $due,
            ], 'Due Successfully Added');
        } catch (\Exception $exception){
            return $this->sendError('Due payment store error', ['error' => $exception->getMessage()]);
        }
    }
}
