<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class VerifyController extends Controller
{
    public function verifyOtp(Request $request) 
    {  

        Validator::make($request->all(), [
            'otp' => 'required',         
        ])->validate();

        if ($request->otp == auth()->user()->getOtp()) {
            auth()->user()->update([
                'isVerified' => 1
            ]);
            return redirect('/home');
        } 
        
        return back()->withErrors('Otp is invalid');

    }

    public function showOtpForm()
    {
        return view('show-otp');
    }
}
