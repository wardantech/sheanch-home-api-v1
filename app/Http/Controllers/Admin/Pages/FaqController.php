<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Models\Pages\Faq;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    use ResponseTrait;

    /**
     * Get all faq lists
     * @param Request $request
     * @return array
     */

    public function getLists(Request $request)
    {
        $columns = ['id', 'title','description','status'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = DB::table('faqs')->where('deleted_at','=',null)
            ->select('id','title','description','status')
            ->orderBy($columns[$column], $dir);

        $count = Faq::count();
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
     * Faq Store
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        //--- Validation Section Starts
        $rules = [
            'title' => 'required|string|max:255',
            'status' => 'required|string',
            'description' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends

        try {
            $faq = new Faq();

            $faq->title = $request->title;
            $faq->status = $request->status;
            $faq->description = $request->description;
            $faq->save();

        }catch (\Exception $exception){
            return $this->sendError('Faq store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Faq status active inactive
     * @param Request $request
     * @param $id
     * @return mixed
     */

    public function changeStatus(Request $request, $id)
    {
        try{
            $faq = Faq::findOrFail($id);
            if($request->status) {
                $faq->status = 0;
                $faq->update();

                return $this->sendResponse(['id'=>$id],'Faq inactive successfully');
            }

            $faq->status = 1;
            $faq->update();

            return $this->sendResponse(['id'=>$id],'Faq active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Faq status error', ['error' => $exception->getMessage()]);
        }
    }
}
