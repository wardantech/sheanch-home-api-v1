<?php

namespace App\Http\Controllers\Admin\Widgets;

use App\Http\Controllers\Controller;
use App\Models\Widgets\HowItWork;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HowItWorkController extends Controller
{
    use ResponseTrait;

    /**
     * Get all How it work lists
     * @param Request $request
     * @return array
     */

    public function getLists(Request $request)
    {
        $columns = ['id', 'title', 'icon', 'description', 'status'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = DB::table('how_it_works')->where('deleted_at','=',null)
            ->select('id', 'title', 'icon', 'description', 'status')
            ->orderBy($columns[$column], $dir);

        $count = HowItWork::count();
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('title', 'like', '%' . $searchValue . '%');
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
     * Property Customer Experience Store
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        //--- Validation Section Starts
        $rules = [
            'title' => 'required',
            'icon' => 'required',
            'description' => 'nullable',
            'status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends

        try {
            $howItWork = new HowItWork();

            $howItWork->title = $request->title;
            $howItWork->icon = $request->icon;
            $howItWork->status = $request->status;
            $howItWork->description = $request->description;
            $howItWork->save();

        }catch (\Exception $exception){
            return $this->sendError('How it work store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * How it work status active inactive
     * @param Request $request
     * @param $id
     * @return mixed
     */

    public function changeStatus(Request $request, $id)
    {
        try{
            $howItWork = HowItWork::findOrFail($id);
            if($request->status) {
                $howItWork->status = 0;
                $howItWork->update();

                return $this->sendResponse(['id'=>$id],'How it work inactive successfully');
            }

            $howItWork->status = 1;
            $howItWork->update();

            return $this->sendResponse(['id'=>$id],'How it work active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('How it work status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * How it work edit
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        try{
            $howItWork = HowItWork::findOrFail($id);
            return $this->sendResponse($howItWork,'How it work data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('How it work data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * How it work Update
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        //--- Validation Section Starts
        $rules = [
            'title' => 'required',
            'icon' => 'required',
            'description' => 'nullable',
            'status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends

        try {
            $howItWork = HowItWork::findOrFail($id);

            $howItWork->title = $request->title;
            $howItWork->icon = $request->icon;
            $howItWork->status = $request->status;
            $howItWork->description = $request->description;
            $howItWork->update();

        }catch (\Exception $exception){
            return $this->sendError('How it work store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * How it work Delete
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try {
            $howItWork = HowItWork::findOrFail($id);
            $howItWork->delete();

            return $this->sendResponse(['id'=>$id],'How it work deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('How it work delete error', ['error' => $exception->getMessage()]);
        }
    }
}
