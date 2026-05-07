<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Ride;

class pageController extends Controller
{
    //
    public function contact()
    {

        return view('contact');
    }

    public function home()
    {

        return view('home');
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function deletaccount()
    {
        return view('deletaccount');
    }

    public function terms()
    {
        return view('terms');
    }
    public function support()
    {
        return view('support');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->phone_number);
        if (!str_starts_with($phone, '92')) {
            $phone = '92' . $phone;
        }
        $phone = '+' . $phone;

        $user = User::where('mobile_number', $phone)->first();

        if ($user) {
            $user->delete();
            return "Account deleted successfully.";
        } else {
            return "No user found with this phone number.";
        }
    }



    public function show($rideId, Request $request) {


      
        $ride = Ride::findOrFail($rideId);

        return view('ride', compact('ride'));
    }

   
}
