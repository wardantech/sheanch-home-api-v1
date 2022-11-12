<?php

namespace App\Http\Controllers\Admin\Accounts;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Accounts\Expanse;

class ExpanseController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $expanse = Expanse::all();

            return $this->sendResponse([
                'expanses' => $expanse
            ], 'Expanses Show Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expanse Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "property_id" => "required|integer",
            "expanse_item_id" => "required|integer",
            "amount" => "required"
        ]);

        try {
            $expanse = new Expanse();

            $expanse->property_id = $data['property_id'];
            $expanse->expanse_item_id = $data['expanse_item_id'];
            $expanse->amount = $data['amount'];
            // $expanse->date = $data['date'];
            $expanse->date = now();
            $expanse->save();

            return $this->sendResponse([
                'expanse' => $expanse
            ], 'Expanse Store Successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Expanse Store Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request,Expanse $expanse)
    {
        $data = $request->validate([
            "property_id" => "required|integer",
            "expanse_item_id" => "required|integer",
            "amount" => "required"
        ]);

        try {
            $expanse->property_id = $data['property_id'];
            $expanse->expanse_item_id = $data['expanse_item_id'];
            $expanse->amount = $data['amount'];
            // $expanse->date = $data['date'];
            $expanse->date = now();
            $expanse->update();

            return $this->sendResponse([
                'expanse' => $expanse
            ], 'Expanse Updated Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expanse Updated Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy(Expanse $expanse)
    {
        try {
            $expanse->delete();

            return $this->sendResponse([
                'expanse' => $expanse
            ], 'Expanse Deleted Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expanse Delete Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }
}
