<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
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
        
       
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        //data storing on session  
        session(['access_token'=>$request->input('_token'),
                 'name' =>$user->name,
                 'user_id'=>$user->id,
                 'company_id' => $user->company_id 
                ]);
        
        // && Hash::check($password, $user->password)
        if ($user && $password == $user->password) {

            $user_details =[
                'access_token' => session('access_token', 'Token not found'),
                'name' => session('name', 'Name not found'),
                'user_id' => session('user_id', 'ID not found'),
                'company_id' => session('company_id', 'Company_id not found'),
            ];
            return view('/dashboard',compact('user_details'));
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // return redirect('dashboard')->with(response());


    }
}
