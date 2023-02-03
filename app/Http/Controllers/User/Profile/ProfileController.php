<?php

namespace App\Http\Controllers\User\Profile;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Landlord;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Settings\Thana;
use App\Models\UserInformation;
use Illuminate\Validation\Rule;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResourse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get Landlord Data
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getUser(Request $request)
    {
        try{
            $user = User::findOrFail($request->id);
            $divisions = Division::select('id', 'name')->get();

            $district = null;
            $thana = null;
            if ($user->information) {
                $district = District::where('division_id', $user->information->division_id)->get();
                $thana = Thana::where('district_id', $user->information->district_id)->get();
            }

            return $this->sendResponse([
                'user' => new ProfileResourse($user),
                'divisions' => $divisions,
                'districts' => $district,
                'thanas' => $thana
            ], 'User data get successfully');
        } catch (\Exception $exception){
            return $this->sendError('User data error', ['error' => $exception->getMessage()]);
        }
    }

    public function sidebar(Request $request)
    {
        try{
            $user = User::select('id', 'name')->findOrFail($request->id);

            $image = null;
            if ($user->information) {
                $image = $user->information->image;
            }

            return $this->sendResponse([
                'user' => $user,
                'image' => $image
            ], 'User data get successfully');
        } catch (\Exception $exception){
            return $this->sendError('User data error', ['error' => $exception->getMessage()]);
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
        $data = $request->validate([
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
            'nid' => 'nullable|string',
            'postal_address' => 'nullable|string',
            'residential_address' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $data['user_id'] = $user->id;

            $user->update($data);

            if ($user->information) {
                $user->information->update($data);
            } else {
                UserInformation::create($data);
            }

            DB::commit();
            return $this->sendResponse(['id'=>$user->id],'User update successfully');
        }catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('User update error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Image upload for landlord
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function imageUpload(Request $request, $id)
    {
        try{
            $imageName = uniqid('user-',false).'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('images'), $imageName);

            $information = UserInformation::where('user_id', $id)->first();
            $information->image = $imageName;
            $information->update();

            return response()->json(['success'=>'You have successfully upload image.']);
        }
        catch (\Exception $exception){
            return $this->sendError('User Image error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * User password change logic
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */

    public function updatePassword(Request $request)
    {
        $request->validate([
            'currentPassword' => 'required',
            'password' => 'required|confirmed'
        ]);

        try {
            $hashedPassword = Auth::user()->password;

            if (Hash::check($request->currentPassword, $hashedPassword)) {
                Auth::user()->update([
                    'password' => Hash::make($request->password)
                ]);

                return [
                    "status"=> true,
                    "message" => "Password change successfully"
                ];
            } else {
                return [
                    "status"=> false,
                    "message" => "Current password not match"
                ];
            }
        }catch (\Exception $exception){
            return $this->sendError('Password change error', ['error' => $exception->getMessage()]);
        }
    }

    public function show(Request $request){
        try{
            $landlord = Landlord::with('division','district','thana')
                ->where('id',$request->landlordId)->first();

            return $this->sendResponse($landlord,'Landlord data get successfully');
        }
        catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('Landlord data error', ['error' => $exception->getMessage()]);
        }
    }
}
