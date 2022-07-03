<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    use ResponseTrait;

    /**
     * List api
     * @return \Illuminate\Http\Response
     */
    public function getList(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Account::select('*')->orderBy($columns[$column], $dir);

        $count = Account::count();

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('account_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('account_type', 'like', '%' . $searchValue . '%')
                    ->orWhere('account_no', 'like', '%' . $searchValue . '%');
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
     * Store api
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //--- Validation Section Start ---//
        $rules = [
            'account_name' => 'required|string',
            'account_no' => 'required|string',
            'account_type' => 'required|string',
            'branch_name' => 'required|string',
            'bank_id' => 'required',
            'initial_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'status' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Account
            $account = new Account();
            $account->account_name = $request->account_name;
            $account->account_no = $request->account_no;
            $account->account_type = $request->account_type;
            $account->branch_name = $request->branch_name;
            $account->bank_id = $request->bank_id;
            $account->initial_balance = $request->initial_balance;
            $account->current_balance = $request->current_balance;
            $account->description = $request->description;
            $account->status = $request->status;
            $account->created_by = Auth::user()->id;
            $account->save();

            return $this->sendResponse(['id'=>$account->id],'Account create successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Account store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $account = Account::findOrFail($id);

            return $this->sendResponse($account,'Account data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Account data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //--- Validation Section Start ---//
        $rules = [
            'account_name' => 'required|string',
            'account_no' => 'required|string',
            'account_type' => 'required|string',
            'branch_name' => 'required|string',
            'bank_id' => 'required',
            'initial_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'status' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Update Account
            $account = Account::findOrFail($id);
            $account->account_name = $request->account_name;
            $account->account_no = $request->account_no;
            $account->account_type = $request->account_type;
            $account->branch_name = $request->branch_name;
            $account->bank_id = $request->bank_id;
            $account->initial_balance = $request->initial_balance;
            $account->current_balance = $request->current_balance;
            $account->description = $request->description;
            $account->status = $request->status;
            $account->updated_by = Auth::user()->id;
            $account->update();

            return $this->sendResponse(['id'=>$account->id],'Account updated successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Account updated error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Status Change the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request, $id)
    {
        try{
            $account = Account::findOrFail($id);

            if($request->status) {
                $account->status = 0;
                $account->update();
                return $this->sendResponse($account,'Account inactive successfully');
            }

            $account->status = 1;
            $account->update();
            return $this->sendResponse($account,'Account active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Account status error', ['error' => $exception->getMessage()]);
        }
    }
}
