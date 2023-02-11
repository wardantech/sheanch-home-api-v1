<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Settings\Utility;

class UtilityController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        //$this->authRepository = $authRepository;
        //$this->middleware(['auth:api'], ['except' => ['getUtilities']]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getList(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Utility::select('*')->orderBy($columns[$column], $dir);

        $count = Utility::count();

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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //--- Validation Section Start ---//
        $rules = [
            'name' => 'required|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Utility
            $utility = new Utility();
            $utility->name = $request->name;
            $utility->description = $request->description;
            $utility->status = $request->status;
            $utility->created_by = Auth::user()->id;
            $utility->save();

            return $this->sendResponse(['id' => $utility->id], 'Utility create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Utility store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $utility = Utility::findOrFail($id);

            return $this->sendResponse($utility, 'Utility data get successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Utility data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //--- Validation Section Start ---//
        $rules = [
            'name' => 'required|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Utility
            $utility = Utility::findOrFail($id);
            $utility->name = $request->name;
            $utility->description = $request->description;
            $utility->status = $request->status;
            $utility->updated_by = Auth::user()->id;
            $utility->update();

            return $this->sendResponse(['id' => $utility->id], 'Utility updated successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Utility updated error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function getUtilities()
    {
        try {
            $utility = Utility::where('status', 1)->get(['id','name']);

            return $this->sendResponse($utility, 'Utility list');

        } catch (\Exception $exception) {

            return $this->sendError('Utility list.', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request, $id)
    {
        try{
            $utility = Utility::findOrFail($id);

            $utility->status = $request->status;
            $utility->update();

            return $this->sendResponse($utility,'Utility category active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Utility category status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $Utility = Utility::findOrFail($id);
            $Utility->delete();

            return $this->sendResponse(['id'=>$id],'Utility deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Utility delete error', ['error' => $exception->getMessage()]);
        }
    }
}
