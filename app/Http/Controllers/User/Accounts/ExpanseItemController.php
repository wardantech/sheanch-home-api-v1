<?php

namespace App\Http\Controllers\User\Accounts;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Accounts\ExpanseItem;
use Illuminate\Support\Facades\Auth;

class ExpanseItemController extends Controller
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

        $query = ExpanseItem::where('created_by', $userId);

        $count = ExpanseItem::count();

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'created_by' => 'required'
        ]);

        try {
            $expanseItem = ExpanseItem::create($data);

            return $this->sendResponse([
                'expanseItems' => $expanseItem
            ], 'Expanse Item Store Successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Expanse Item Store Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function edit(Request $request)
    {
        try {
            $expanseItem = ExpanseItem::findOrFail($request->id);

            return $this->sendResponse([
                'expanseItem' => $expanseItem
            ], 'Succssfully get expance item');

        } catch (\Exception $exception) {
            return $this->sendError('Add Payment Method Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'updated_by' => 'required'
        ]);

        try {
            $expanseItem = ExpanseItem::findOrFail($id);
            $expanseItem->update($data);

            return $this->sendResponse([
                'expanseItem' => $expanseItem
            ], 'Expanse Item Updated Successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expanse Item Updated Error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $expanseItem = ExpanseItem::findOrFail($id);
        $expanseItem->delete();

        return $this->sendResponse('', 'Expanse item delete successfully');
    }
}
