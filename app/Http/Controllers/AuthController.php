<?php

namespace App\Http\Controllers;
use Validator, Hash, Auth;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(){
        // $this->middleware('auth')->only('verify');
    }

    public function verify(Request $request){
        $user = Auth::user();

        if(!$user){
            return response()->json(['status' => 'fail', 'data' => ['error' => 'Session not authenticated.']], 401);
        }

        return response()->json(['status' => 'success', 'data' => ['session' => 'User authenticated', 'user' => $user]], 200);
    }

    public function logout(Request $request){
        Auth::logout();

        return response()->json(['status' => 'success', 'data' => ['session' => 'User has been logged out.', 'user' => '']], 200);
    }

    public function login(Request $request){
        $loginCreds = $request->only('email','password');

        //If the credentials provided do not match with any user account, then provide authentication error message
        if(!Auth::attempt($loginCreds, true)) {
            return response()->json(['status' => 'fail', 'data' => ['login' => 'Unable to authenticate credentials.']], 401);
        }

        // If authentication attempt is successful, then create session.
        $user = Auth::user();

        // Verify the account is active.
        if($user->active !== 1){
            Auth::logout();
            return response()->json(['status' => 'fail', 'data' => ['login' => 'Account is not active. Please contact your administrator.']], 401);
        }else{
            return response()->json(['status' => 'success', 'data' => ['login' => 'Account authenticated!', 'user' => $user]], 200);
        }
    }
}