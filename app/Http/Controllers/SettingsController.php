<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Compnay;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class SettingsController extends Controller
{

    public function index(){
        $userDetails = Session('user_details');
        $companyId = $userDetails['company_id'];


        $company = Company::where('company_id',$companyId)->first();
        
            if ($userDetails && isset($userDetails['user_id'])) {
                // Retrieve the 'role' from user details
                $userId = $userDetails['user_id'];
                $User = User::where('id', $userId)->first();
                return view('settings', [         
                    'userDetails' => $User,
                    'company' => $company
                    ]
                );
                // Now $userRole contains the role of the currently logged-in user
                // You can use $userRole as needed in your controller method
            }
}
}
