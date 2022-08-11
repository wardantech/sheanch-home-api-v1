<?php

namespace App\Http\Controllers\User\Widgets;

use App\Http\Controllers\Controller;
use App\Models\Widgets\HowItWork;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    use ResponseTrait;

    /**
     * Get all how to works
     * @return array|\Illuminate\Http\Response
     */

    public function getHowToWork()
    {
        try {
            $howToWorks = HowItWork::select('id', 'title', 'icon', 'description', 'status')
                    ->where('status', 1)->limit(3)->get();

            if(count($howToWorks) > 0) {
                return [
                    'status' => true,
                    'data' => $howToWorks
                ];
            }

            return [
                'status' => false,
                'data' => 'Data Not Found'
            ];

        } catch (\Exception $exception) {
            return $this->sendError('How to work get data error', ['error' => $exception->getMessage()]);
        }
    }
}
