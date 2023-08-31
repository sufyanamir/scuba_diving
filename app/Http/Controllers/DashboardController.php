<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $user_details = session('user_details');
        return view('dashboard', [
            'userDetails' => $user_details,
        ]);
    }
}
