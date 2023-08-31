<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;


class TestController extends Controller
{
    public function index(Request $request){

        $keys = ['access_token', 'name', 'user_id','company_id'];
        $user_details = [];
    
        foreach ($keys as $key) {
            $user_details[$key] = $request->session()->get($key);
        }
    
    
        return view('dashboard',compact('user_details'));
    
    }

}