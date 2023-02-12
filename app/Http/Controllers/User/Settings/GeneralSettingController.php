<?php

namespace App\Http\Controllers\User\Settings;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Settings\Division;
use App\Models\Widgets\HowItWork;
use App\Models\Property\PropertyAd;
use App\Http\Controllers\Controller;
use App\Models\Settings\PropertyType;
use App\Models\Settings\FrontendSetting;
use App\Http\Resources\FrontPropertiesResourse;

class GeneralSettingController extends Controller
{
    use ResponseTrait;

    public function home(Request $request)
    {
        $frontendData = FrontendSetting::with('media')->first();
        $propertyTypes = PropertyType::select('id', 'name')->get();
        $divisions = Division::select('id', 'name')->get();

        $worksWidgets = HowItWork::select('id', 'title', 'icon', 'description', 'status')
                    ->where('status', 1)
                    ->limit(3)
                    ->get();

        $properties = PropertyAd::where('status', 1)
                ->with('property')
                ->latest()
                ->paginate(6);

        return $this->sendResponse([
            'frontendData' => $frontendData,
            'propertyTypes' => $propertyTypes,
            'divisions' => $divisions,
            'worksWidgets' => $worksWidgets,
            'properties' => FrontPropertiesResourse::collection($properties)
        ],'');
    }
}
