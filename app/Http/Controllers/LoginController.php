<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }
    public function login(Request $request)
    {



        $email = $request->input('email');
        $token = $request->input('_token');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();
        // && Hash::check($password, $user->password)
        if ($user && $password == $user->password) {

            $user_details =[
                'token' => $token,
                'name' => $user->name,
                'user_id' => $user->id,
                'company_id' => $user->company_id,
            ];
            return view('dashboard',compact('user_details'));
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // return redirect('dashboard')->with(response());


    }
}
