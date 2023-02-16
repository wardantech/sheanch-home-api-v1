<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Models\Pages\PropertyFaq;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyFaqController extends Controller
{
    use ResponseTrait;

    /**
     * Get all PropertyFaq lists
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

        $query = DB::table('property_faqs')->where('deleted_at','=',null)
            ->select('id','title','description','status')
            ->orderBy($columns[$column], $dir);

        $count = PropertyFaq::count();
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
     * PropertyFaq Store
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'required|string|max:255',
            'status' => 'required',
            'description' => 'nullable'
        ]);

        try {
            $propertyFaq = PropertyFaq::create($data);

            return $this->sendResponse([
                'propertyFaq'=>$propertyFaq
            ],'Property faq store successfully');
        }catch (\Exception $exception){
            return $this->sendError('Property faq store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Property faq status active inactive
     * @param Request $request
     * @param $id
     * @return mixed
     */

    public function changeStatus(Request $request, $id)
    {
        try{
            $propertyFaq = PropertyFaq::findOrFail($id);
            if($request->status) {
                $propertyFaq->status = 0;
                $propertyFaq->update();

                return $this->sendResponse(['id'=>$id],'Property faq inactive successfully');
            }

            $propertyFaq->status = 1;
            $propertyFaq->update();

            return $this->sendResponse(['id'=>$id],'Property faq active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Property faq status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Property faq edit
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        try{
            $propertyFaq = PropertyFaq::findOrFail($id);
            return $this->sendResponse($propertyFaq,'Property faq data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Property faq data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Property faq Update
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $data = $this->validate($request, [
            'title' => 'required|string|max:255',
            'status' => 'required',
            'description' => 'nullable'
        ]);

        try {
            $propertyFaq = PropertyFaq::findOrFail($id);
            $propertyFaq->update($data);

            return $this->sendResponse([
                'propertyFaq'=>$propertyFaq
            ],'Property faq update successfully');
        }catch (\Exception $exception){
            return $this->sendError('Property faq store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Property faq Delete
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try {
            $propertyFaq = PropertyFaq::findOrFail($id);
            $propertyFaq->delete();

            return $this->sendResponse(['id'=>$id],'Property faq deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Property faq delete error', ['error' => $exception->getMessage()]);
        }
    }
}
