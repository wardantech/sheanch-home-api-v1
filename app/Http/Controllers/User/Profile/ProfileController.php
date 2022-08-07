<?php

namespace App\Http\Controllers\User\Profile;

use App\Http\Controllers\Controller;
use App\Models\Landlord;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Models\Settings\Thana;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get Landlord
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function getLandlordData(Request $request)
    {
        try{
            $landlord = Landlord::findOrFail($request->id);

            $divisions = Division::select('id', 'name')->get();
            $district = District::where('division_id', $landlord->division_id)->get();
            $thana = Thana::where('district_id', $landlord->district_id)->get();

            return $this->sendResponse([
                'landlord' => $landlord,
                'divisions' => $divisions,
                'districts' => $district,
                'thanas' => $thana,
            ], 'Get all data successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Landlord data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * landlord update
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        //--- Validation Section Starts
        $rules = [
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request, $id) {
                    return $query->whereNull('deleted_at')->where('id','==',$id);
                })
            ],

            'name' => 'required|string|max:255',
            'mobile' => 'required|string',
            'thana_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'division_id' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends


        DB::beginTransaction();
        try {

            $landlord = Landlord::findOrFail($id);

            $landlord->name = $request->name;
            $landlord->email = $request->email;
            $landlord->mobile = $request->mobile;
            $landlord->status = $request->status;
            $landlord->nid = $request->nid;
            $landlord->thana_id = $request->thana_id;
            $landlord->district_id = $request->district_id;
            $landlord->division_id = $request->division_id;
            $landlord->postal_address = $request->postal_address;
            $landlord->residential_address = $request->residential_address;

            $landlord->update();

            $user = new User();
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->name = $request->name;
            $user->status = $request->status;

            $user->update();

            DB::commit();

            return $this->sendResponse(['id'=>$landlord->id],'Landlord update successfully');

        }catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('Landlord update error', ['error' => $exception->getMessage()]);
        }
    }

    public function imageUpload(Request $request, $id)
    {
        try{
            $imageName = uniqid('landlord-',false).'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('images'), $imageName);

            $landlord = Landlord::findOrFail($id);
            $landlord->image = $imageName;
            $landlord->update();

            return response()->json(['success'=>'You have successfully upload file.']);
        }
        catch (\Exception $exception){
            return $this->sendError('Landlord Image error', ['error' => $exception->getMessage()]);
        }
    }
}
