<?php

namespace App\Http\Controllers\Admin\Expense;

use App\Http\Controllers\Controller;

use App\Models\Expense\ExpenseCategory;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        //$this->authRepository = $authRepository;
        //$this->middleware(['auth:api'], ['except' => ['login','register']]);
    }

    public function getList(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = ExpenseCategory::select('*')->orderBy($columns[$column], $dir);

        $count = ExpenseCategory::count();

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        if($length!='all'){
            $fetchData = $query->paginate($length);
        }
        else{
            $fetchData = $query->paginate($count);
        }

        return ['data' => $fetchData, 'draw' => $request['params']['draw']];
    }

    /**
     * Store Expense category
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        //--- Validation Section Start ---//
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required',
            'description' => 'string|nullable'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Utilities Category
            $category = new ExpenseCategory();

            $category->name = $request->name;
            $category->status = $request->status;
            $category->description = $request->description;
            $category->created_by = Auth::user()->id;
            $category->save();

            return $this->sendResponse(['id'=>$category->id],'Expense category create successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expense category store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Expense category single data get for update or show
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try{
            $category = ExpenseCategory::findOrFail($id);

            return $this->sendResponse($category,'Expense category data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Expense category data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Update Expense category
     * @param Request $request
     * @param $id
     */

    public function update(Request $request, $id)
    {
        //--- Validation Section Start ---//
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required',
            'description' => 'string|nullable'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Facilities Category
            $category = ExpenseCategory::findOrFail($id);

            $category->name = $request->name;
            $category->status = $request->status;
            $category->description = $request->description;
            $category->updated_by = Auth::user()->id;
            $category->update();

            return $this->sendResponse(['id'=>$category->id],'Expense category updated successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expense category update error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * For Active | Inactive Utility categories
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function changeStatus(Request $request, $id)
    {
        try{
            $category = ExpenseCategory::findOrFail($id);

            if($request->status) {
                $category->status = 0;
                $category->update();
                return $this->sendResponse($category,'Expense category inactive successfully');
            }

            $category->status = 1;
            $category->update();
            return $this->sendResponse($category,'Expense category active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Expense category status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * ExpenseCategory Data Delete
     * @param $id
     * @return mixed
     */

    public function destroy($id)
    {
        try {
            $expenseCategory = ExpenseCategory::findOrFail($id);
            $expenseCategory->delete();

            return $this->sendResponse(['id'=>$id],'Expense category deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Expense category delete error', ['error' => $exception->getMessage()]);
        }
    }
}
