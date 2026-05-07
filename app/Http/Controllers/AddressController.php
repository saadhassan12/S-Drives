<?php

namespace App\Http\Controllers;

use App\Models\FavAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    //

    public function favorite_addresses(Request $request)
    {

        $user = Auth::user();
        $request->validate([
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-90,90',
        ]);
        $FavAddress = FavAddress::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'address' => $request->address,
            'name' => $request->name,
            'lat' => $request->lat,
            'long' => $request->long,
        ]);
        return apiResponse([
            'FavAddress' => $FavAddress,
        ], 'Fav Address has been created successfully');
    }

    public function get_favorite_addresses()
    {
        $user = Auth::user();
        $getfav = FavAddress::where('user_id',$user->id)->get();
        return apiResponse( $getfav,
         'All Fav Address');
    }
      public function delete_favorite_addresses($id)
    {
        $favoriteAddress = FavAddress::find($id);
        if (!$favoriteAddress) {
            return apiResponse(null, 'Favorite address not found');
        }
        $favoriteAddress->delete();

        return apiResponse(null,'Favorite address deleted successfully');
    }
}
