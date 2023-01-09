<?php

namespace App\Http\Controllers\User\Property;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Property\PropertyDeed;
use App\Models\Property\DeedInformation;
use App\Http\Requests\StoreDeedInfoRequest;
use App\Http\Resources\DeedInformationResource;

class DeedInformationController extends Controller
{
    use ResponseTrait;

    public function getData(Request $request)
    {
        try {
            $deed = PropertyDeed::findOrFail($request->deedId);

            return $this->sendResponse([
                'information' => new DeedInformationResource($deed)
            ], 'Deed property information get successfully.');
        }catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }

    public function store(StoreDeedInfoRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $data['emergency_contact'] = json_encode($data['emergency_contact']);
            $data['previus_landlord'] = json_encode($data['previus_landlord']);
            $data['family_members'] = json_encode($data['family_members']);
            $data['home_servant'] = json_encode($data['home_servant']);
            $data['driver'] = json_encode($data['driver']);

            $deedInfo = DeedInformation::create($data);
            $deed = PropertyDeed::findOrFail($data['property_deed_id']);
            $deed->status = 4;
            $deed->update();

            DB::commit();
            return $this->sendResponse([
                'information' => $deedInfo
            ], 'information successfully store.');
        }catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }

    public function imageUpload(Request $request, $id)
    {
        try{
            $imageName = uniqid('tenent-photo-',false).'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('images'), $imageName);

            $information = DeedInformation::findOrFail($id);
            $information->image = $imageName;
            $information->update();

            return response()->json(['success'=>'You have successfully upload image.']);
        }
        catch (\Exception $exception){
            return $this->sendError('Deed Information Image error', ['error' => $exception->getMessage()]);
        }
    }
}
