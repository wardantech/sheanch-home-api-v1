<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\PropertyType;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
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

        $query = PropertyType::select('*')->orderBy($columns[$column], $dir);

        $count = PropertyType::count();

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
     * Store Property Type
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|string|max:255',
            'status' => 'required',
            'description' => 'string|nullable'
        ]);

        try {
            $data['created_by'] = Auth::user()->id;
            $category = PropertyType::create($data);

            return $this->sendResponse(['id'=>$category->id],'Property Type create successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Property Type store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Property Type single data get for update or show
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = PropertyType::findOrFail($id);
        return $this->sendResponse($category,'Property Type data get successfully');
    }

    /**
     * Update Property Type
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {
        $data = $this->validate($request, [
            'name' => 'required|string|max:255',
            'status' => 'required',
            'description' => 'string|nullable'
        ]);

        try {
            $data['updated_by'] = Auth::user()->id;
            $category = PropertyType::findOrFail($id);
            $category->update($data);

            return $this->sendResponse([
                'id'=>$category->id
            ],'Property Type updated successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Property Type update error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * For Active | Inactive Property Type
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        try{
            $propertyType = PropertyType::findOrFail($id);

            $propertyType->status = $request->status;
            $propertyType->update();

            return $this->sendResponse($propertyType,'Property Type active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Property Type status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Property Type Data Delete
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            $propertyType = PropertyType::findOrFail($id);
            $propertyType->delete();

            return $this->sendResponse(['id'=>$id],'Property type deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Property type delete error', ['error' => $exception->getMessage()]);
        }
    }
}
