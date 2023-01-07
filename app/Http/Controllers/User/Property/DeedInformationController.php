<?php

namespace App\Http\Controllers\User\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeedInformationController extends Controller
{
    public function store(Request $request)
    {
        return $request->input();
    }
}
