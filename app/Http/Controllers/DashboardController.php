<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Services;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $customersCount=Customers::count();
        $servicesCount=Services::count();
        $adminCount = User::where('user_role',1)->count();
        $staffCount = User::where('user_role',2)->count();
        $user_details = session('user_details');
        return view('dashboard', [
            'userDetails' => $user_details,
            'adminCount'=>$adminCount,
            'customersCount'=>$customersCount,
            'staffCount'=>$staffCount,
            'servicesCount'=>$servicesCount,
        ]);
    }
}
