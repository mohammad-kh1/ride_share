<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function submit(Request $request)
    {
        //validate phone number
        $request->validate([
            'email' => 'required|email'
        ]);

        //find or create a user model
        $user = User::firstOrCreate([
            'email' => $request->email
        ]);

        if(!$user)
        {
            return response()->json(["message" => "Could Not Proccess a user with that phone number"] , 401);
        }

        // send the user a one time code 
        $user->notify(new LoginNeedsVerification());

        //return back a response
        return response()->json(['message' => 'OTP is sent'] );
    }

    public function verifiy(Request $request)
    {
        //validate request
        $request->validate([
            'email' => 'required|email',
            'login_code' => 'required|numeric|between:11111,99999'
        ]);
        // find user
        $user = User::where('email' , $request->email)->where('login_code' , $request->login_code)->first();

        // is the code provied

        //if so return back an auth token
        if($user)
        {
            $user->update([
                'login_code' => $request->email
            ]);
            return $user->createToken($request->login_code)->plainTextToken;
        }

        // if not return back a message
        return response()->json(['message'=>'Invalid verification code'] , 401);
    }

}
