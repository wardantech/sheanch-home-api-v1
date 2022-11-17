<?php

namespace App\Http\Controllers\Admin\Accounts;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Accounts\Revenue;
use App\Http\Controllers\Controller;
use App\Models\Accounts\Transaction;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $revenues = Revenue::all();

            return $this->sendResponse([
                'revenues' => $revenues
            ], 'Revenues Show Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Revenues Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => 'required|integer',
            'remark' => 'nullable|string',
            'amount' => 'required'
        ]);

        DB::beginTransaction();
        try {
            // Store in revenues table
            $revenue = new Revenue();

            $revenue->property_id = $data['property_id'];
            $revenue->amount = $data['amount'];
            // $expanse->date = $data['date'];
            $revenue->date = now();
            $revenue->save();

            // Store in transactions table
            $transaction = new Transaction();

            $transaction->property_id = $data['property_id'];
            $transaction->revenue_id = $revenue->id;
            $transaction->cash_in = $data['amount'];
            $transaction->remark = $data['remark'];
            $transaction->transaction_purpose = 1;
            // $transaction->date = $data['date'];
            $transaction->date = now();
            $transaction->save();

            DB::commit();
            return $this->sendResponse([
                'revenue' => $revenue
            ], 'Revenue Store Successfully');

        }catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Revenue Store Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request,Revenue $revenue)
    {
        $data = $request->validate([
            'property_id' => 'required|integer',
            'remark' => 'nullable|string',
            'amount' => 'required'
        ]);

        DB::beginTransaction();
        try {
            // Update revenues table data
            $revenue->property_id = $data['property_id'];
            $revenue->amount = $data['amount'];
            // $expanse->date = $data['date'];
            $revenue->date = now();
            $revenue->update();

            // Update revenues table data
            $transaction = Transaction::where('revenue_id', $revenue->id)->first();

            $transaction->property_id = $data['property_id'];
            $transaction->revenue_id = $revenue->id;
            $transaction->cash_in = $data['amount'];
            $transaction->remark = $data['remark'];
            $transaction->transaction_purpose = 1;
            // $transaction->date = $data['date'];
            $transaction->date = now();
            $transaction->update();

            DB::commit();
            return $this->sendResponse([
                'revenue' => $revenue
            ], 'Revenue Updated Successfully');

        }catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Revenue Updated Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy(Revenue $revenue)
    {
        DB::beginTransaction();
        try {
            Transaction::where('revenue_id', $revenue->id)->delete();
            $revenue->delete();

            DB::commit();
            return $this->sendResponse([
                'revenue' => $revenue
            ], 'Revenue Deleted Successfully');
        }catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Revenue Delete Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }
}
