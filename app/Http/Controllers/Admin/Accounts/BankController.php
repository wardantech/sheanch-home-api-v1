<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Bank;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        //$this->authRepository = $authRepository;
        //$this->middleware(['auth:api'], ['except' => ['login','register']]);
    }


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

        $query = Bank::select('*')->orderBy($columns[$column], $dir);

        $count = Bank::count();

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
     * Store api
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //--- Validation Section Start ---//
        $rules = [
            'name' => 'required|string',
            'status' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Bank
            $bank = new Bank();
            $bank->name = $request->name;
            $bank->description = $request->description;
            $bank->status = $request->status;
            $bank->created_by = Auth::user()->id;
            $bank->save();

            return $this->sendResponse(['id'=>$bank->id],'Bank create successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Bank store error', ['error' => $exception->getMessage()]);
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
            $bank = Bank::findOrFail($id);

            return $this->sendResponse($bank,'Bank data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Bank data error', ['error' => $exception->getMessage()]);
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
            'name' => 'required|string',
            'status' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Update Bank
            $bank = Bank::findOrFail($id);
            $bank->name = $request->name;
            $bank->description = $request->description;
            $bank->status = $request->status;
            $bank->updated_by = Auth::user()->id;
            $bank->update();

            return $this->sendResponse(['id'=>$bank->id],'Bank updated successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Bank updated error', ['error' => $exception->getMessage()]);
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
            $bank = Bank::findOrFail($id);

            if($request->status) {
                $bank->status = 0;
                $bank->update();
                return $this->sendResponse($bank,'Bank inactive successfully');
            }

            $bank->status = 1;
            $bank->update();
            return $this->sendResponse($bank,'Bank active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Bank status error', ['error' => $exception->getMessage()]);
        }
    }
}
