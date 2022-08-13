<?php

namespace App\Http\Controllers\User\Property;

use App\Http\Controllers\Controller;
use App\Models\Pages\AboutPropertySelling;
use App\Models\Pages\PropertyCustomerExperience;
use App\Models\Pages\PropertyFaq;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class PropertyPageController extends Controller
{
    use ResponseTrait;

    public function getCustomerExperiences()
    {
        try {
            $customerExperience = PropertyCustomerExperience::select('id', 'video_link', 'status')
                ->where('status', 1)->limit(3)->get();

            return $this->sendResponse($customerExperience, 'Customer experiences get data get successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Customer experiences get data error', ['error' => $exception->getMessage()]);
        }
    }

    public function getFaq()
    {
        try {
            $faq = PropertyFaq::select('id', 'title', 'description', 'status')
                ->where('status', 1)->limit(10)->get();

            return $this->sendResponse($faq, 'Faq get data get successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Faq get data error', ['error' => $exception->getMessage()]);
        }
    }

    public function getAboutPropertySelling()
    {
        try {
            $aboutPropertySelling = AboutPropertySelling::where('status', 1)->limit(3)->get();

            return $this->sendResponse($aboutPropertySelling, 'Faq get data get successfully');

        } catch (\Exception $exception) {
            return $this->sendError(
                'About property selling get data error',
                ['error' => $exception->getMessage()
            ]);
        }
    }
}
