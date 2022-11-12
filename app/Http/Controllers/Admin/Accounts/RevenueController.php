<?php

namespace App\Http\Controllers\Admin\Accounts;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Accounts\Revenue;
use App\Http\Controllers\Controller;

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
            "property_id" => "required|integer",
            "amount" => "required"
        ]);

        try {
            $revenue = new Revenue();

            $revenue->property_id = $data['property_id'];
            $revenue->amount = $data['amount'];
            // $expanse->date = $data['date'];
            $revenue->date = now();
            $revenue->save();

            return $this->sendResponse([
                'revenue' => $revenue
            ], 'Revenue Store Successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Revenue Store Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request,Revenue $revenue)
    {
        $data = $request->validate([
            "property_id" => "required|integer",
            "amount" => "required"
        ]);

        try {
            $revenue->property_id = $data['property_id'];
            $revenue->amount = $data['amount'];
            // $expanse->date = $data['date'];
            $revenue->date = now();
            $revenue->update();

            return $this->sendResponse([
                'revenue' => $revenue
            ], 'Revenue Updated Successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Revenue Updated Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy(Revenue $revenue)
    {
        try {
            $revenue->delete();

            return $this->sendResponse([
                'revenue' => $revenue
            ], 'Revenue Deleted Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Revenue Delete Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }
}
