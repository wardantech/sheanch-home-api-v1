<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounts\ExpanseItem;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ExpanseItemController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $expanseItem = ExpanseItem::all();

            return $this->sendResponse([
                'expanseItems' => $expanseItem
            ], 'Expanse Item Show Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expanse Item Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:expanse_items,name'
        ]);

        try {
            $expanseItem = new ExpanseItem();

            $expanseItem->name = $data['name'];
            $expanseItem->save();

            return $this->sendResponse([
                'expanseItems' => $expanseItem
            ], 'Expanse Item Store Successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Expanse Item Store Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request,ExpanseItem $expanseItem)
    {
        $data = $request->validate([
            'name' => 'required|unique:expanse_items,name,' . $expanseItem->id
        ]);

        try {
            $expanseItem->name = $data['name'];
            $expanseItem->update();

            return $this->sendResponse([
                'expanseItem' => $expanseItem
            ], 'Expanse Item Updated Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expanse Item Updated Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy(ExpanseItem $expanseItem)
    {
        try {
            $expanseItem->delete();

            return $this->sendResponse([
                'expanseItem' => $expanseItem
            ], 'Expanse Item Deleted Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expanse Item Delete Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }
}
