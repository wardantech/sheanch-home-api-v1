<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Models\Pages\AboutPropertySelling;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AboutPropertySellingController extends Controller
{
    use ResponseTrait;

    /**
     * Get all about property selling lists
     * @param Request $request
     * @return array
     */

    public function getLists(Request $request)
    {
        $columns = ['id', 'title', 'description', 'status'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = DB::table('about_property_sellings')->where('deleted_at','=',null)
            ->select('id', 'title', 'description', 'status')
            ->orderBy($columns[$column], $dir);

        $count = AboutPropertySelling::count();
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
     * About property selling Store
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        //--- Validation Section Starts
        $rules = [
            'title' => 'required|string|max:255',
            'status' => 'required',
            'description' => 'nullable'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends

        try {
            $propertySelling = new AboutPropertySelling();

            $propertySelling->title = $request->title;
            $propertySelling->status = $request->status;
            $propertySelling->description = $request->description;
            $propertySelling->save();

            return $this->sendResponse(['id'=> $propertySelling->id],'About property selling create successfully');

        }catch (\Exception $exception){
            return $this->sendError('About property selling store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * About Property Selling status active inactive
     * @param Request $request
     * @param $id
     * @return mixed
     */

    public function changeStatus(Request $request, $id)
    {
        try{
            $propertySelling = AboutPropertySelling::findOrFail($id);
            if($request->status) {
                $propertySelling->status = 0;
                $propertySelling->update();

                return $this->sendResponse(['id'=>$id],'About property selling inactive successfully');
            }

            $propertySelling->status = 1;
            $propertySelling->update();

            return $this->sendResponse(['id'=>$id],'About property selling active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('About property selling status error', ['error' => $exception->getMessage()]);
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
            $propertySelling = AboutPropertySelling::findOrFail($id);

            return $this->sendResponse($propertySelling, 'About property selling edit data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('About property selling data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * About property selling Update
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        //--- Validation Section Starts
        $rules = [
            'title' => 'required|string|max:255',
            'status' => 'required',
            'description' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends

        try {
            $propertySelling = AboutPropertySelling::findOrFail($id);

            $propertySelling->title = $request->title;
            $propertySelling->status = $request->status;
            $propertySelling->description = $request->description;
            $propertySelling->update();

            return $this->sendResponse(['id' => $propertySelling->id], 'About property selling updated successfully');

        }catch (\Exception $exception){
            return $this->sendError('About property selling store error', ['error' => $exception->getMessage()]);
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
            $propertySelling = AboutPropertySelling::findOrFail($id);
            $propertySelling->delete();

            return $this->sendResponse(['id'=>$id],'About property selling deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('About property selling delete error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Image upload
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function imageUpload(Request $request, $id)
    {
        try{
            $imageName = uniqid('about-property',false).'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('images'), $imageName);

            $propertySelling = AboutPropertySelling::findOrFail($id);
            $propertySelling->image = $imageName;
            $propertySelling->update();

            return response()->json(['success'=>'You have successfully upload file.']);
        }
        catch (\Exception $exception){
            return $this->sendError('About Property Selling Image error', ['error' => $exception->getMessage()]);
        }
    }
}
