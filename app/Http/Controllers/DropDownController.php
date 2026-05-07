<?php

namespace App\Http\Controllers;

use App\Models\VehicleCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DropDownController extends Controller
{
    //
    public function getAllCategories()
    {
        $categories = VehicleCategory::all();
        return apiResponse($categories, 'All Vehicle Categories', 200, true);
    }

    public function getmethod()
    {
        $getmethod = DB::table('cash')->get();

        return apiResponse($getmethod, 'All Method', 200, true);

    }
}
