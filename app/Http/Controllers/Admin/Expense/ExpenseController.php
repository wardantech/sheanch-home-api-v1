<?php

namespace App\Http\Controllers\Admin\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense\Expense;
use App\Models\Expense\ExpenseCategory;
use App\Models\Settings\PropertyType;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
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

        $query = Expense::select('*')->orderBy($columns[$column], $dir);

        $count = Expense::count();

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
        //return $request->all();
        //--- Validation Section Start ---//
        $rules = [
            'expense_category_id' => 'required|integer',
            'status' => 'required',
            'total_amount' => 'required',
            'description' => 'string|nullable'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Utilities Category
            $expense = new Expense();

            $expense->expense_category_id = $request->expense_category_id;
            $expense->total_amount = $request->total_amount;
            $expense->status = $request->status;
            $expense->details = $request->description;
            $expense->description = $request->description;
            $expense->created_by = Auth::user()->id;
            $expense->save();

            return $this->sendResponse(['id'=>$expense->id],'Expense create successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expense store error', ['error' => $exception->getMessage()]);
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
            $category = Expense::findOrFail($id);

            return $this->sendResponse($category,'Expense data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Expense data error', ['error' => $exception->getMessage()]);
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
            'expense_category_id' => 'required|integer',
            'status' => 'required',
            'total_amount' => 'required',
            'description' => 'string|nullable'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Facilities Category
            $category = Expense::findOrFail($id);

            $category->name = $request->name;
            $category->status = $request->status;
            $category->description = $request->description;
            $category->updated_by = Auth::user()->id;
            $category->update();

            return $this->sendResponse(['id'=>$category->id],'Expense updated successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Expense update error', ['error' => $exception->getMessage()]);
        }
    }

    public function getCategories()
    {
        try {
            $expenseCategories = ExpenseCategory::where('status', true)->get();

            return $this->sendResponse($expenseCategories, 'Expense categories list');

        } catch (\Exception $exception) {

            return $this->sendError('Expense categories list.', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * For Active | Inactive Utility categories
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function status(Request $request, $id)
    {
        try{
            $category = Expense::findOrFail($id);

            if($request->status) {
                $category->status = 0;
                $category->update();
                return $this->sendResponse($category,'Expense  inactive successfully');
            }

            $category->status = 1;
            $category->update();
            return $this->sendResponse($category,'Expense active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Expense status error', ['error' => $exception->getMessage()]);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try{
            $category = Expense::findOrFail($id);

            if($request->status) {
                $category->status = 0;
                $category->update();
                return $this->sendResponse($category,'Expense inactive successfully');
            }

            $category->status = 1;
            $category->update();
            return $this->sendResponse($category,'Expense active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Expense category status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Expense Data Delete
     * @param $id
     * @return mixed
     */

    public function destroy($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->delete();

            return $this->sendResponse(['id'=>$id],'Expense deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Expense delete error', ['error' => $exception->getMessage()]);
        }
    }
}
