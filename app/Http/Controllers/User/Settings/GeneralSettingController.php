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
use App\Models\Area;

class GeneralSettingController extends Controller
{
    use ResponseTrait;

    public function home(Request $request)
    {
        $frontendData = FrontendSetting::with('media')->first();
        $propertyTypes = PropertyType::select('id', 'name')->get();
        $divisions = Division::select('id', 'name')->get();
        $areas = Area::select('id', 'name')->get();

        $worksWidgets = HowItWork::select('id', 'title', 'icon', 'description', 'status')
                    ->where('status', 1)
                    ->limit(3)
                    ->get();

        $properties = PropertyAd::where('status', 1)
                ->with('property')
                ->latest()
                ->paginate(6);

        return $this->sendResponse([
            'areas' => $areas,
            'frontendData' => $frontendData,
            'propertyTypes' => $propertyTypes,
            'divisions' => $divisions,
            'worksWidgets' => $worksWidgets,
            'properties' => FrontPropertiesResourse::collection($properties)
        ],'');
    }

    public function properties(Request $request)
    {
        $properties = PropertyAd::with('property')->where('status', 1);

        if (isset($request->sale_type)) {
            $properties->where('sale_type', $request->input('sale_type'));
        }
        if (isset($request->area_id)) {
            $properties->where('area_id', $request->input('area_id'));
        }
        if (isset($request->property_type_id)) {
            $properties->where('property_type_id', $request->input('property_type_id'));
        }
        if (isset($request->min_price)) {
            $properties->where('rent_amount', '>=', $request->min_price);
        }
        if (isset($request->max_price)) {
            $properties->where('rent_amount', '<=', $request->max_price);
        }

        // get search result data
        $searchResult = $properties->get();

        return $this->sendResponse([
            'properties' => FrontPropertiesResourse::collection($searchResult)
        ],'');
    }
}
