<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRequests;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ServiceRequestsController extends Controller
{

    public function index()
    {
        $user_details = session('user_details');

        $requests = ServiceRequests::all();

        return view('service_requests', ['requests' => $requests, 'userDetails' => $user_details]);
    }

    public function makeRequest(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'req_name' => 'required|string|max:255',
                'req_company_name' => 'required|string|max:255',
                'req_email' => 'required|email|max:255|unique:users,email|unique:service_requests,req_email',
                'req_address' => 'required|string|max:255',
            ]);

            // Create a new ServiceRequests instance and fill it with the validated data
            $serviceRequest = new ServiceRequests([
                'req_name' => $validatedData['req_name'],
                'req_company_name' => $validatedData['req_company_name'],
                'req_email' => $validatedData['req_email'],
                'req_address' => $validatedData['req_address'],
            ]);

            // Save the record to the database
            $serviceRequest->save();

            // Optionally, you can return a response or redirect to a success page
            return response()->json(['success' => true, 'message' => 'Service request added successfully! You will be notified through E-mail.'], 200);
        } catch (\Exception $e) {
            // Handle other exceptions, such as database errors
            return response()->json(['success' => false, 'message' => 'An error occurred while adding the service request.', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $requestedCompany = DB::table('service_requests')->where('req_id', $id)->first();

            $companyId = DB::table('company')->insertGetId([
                'company_name' => $requestedCompany->req_company_name,
                'company_address' => $requestedCompany->req_address,

            ]);
            DB::table('users')->insert([
                'name' => $requestedCompany->req_name,
                'email' => $requestedCompany->req_email,
                'address' => $requestedCompany->req_address,
                'password' => md5('12345'),
                'company_id' => $companyId,
                'user_role' => '1',

            ]);

            DB::table('service_requests')->where('req_id', $id)->delete();

            return redirect('/requests')->with('success', 'Request approved successfully');
        } catch (\Exception $e) {
            return redirect('/requests')->with('error', $e->getMessage());
        }
    }

    public function destroy($id){
        $request = ServiceRequests::find($id);

        $request->delete();

        return redirect()->back()->with('success', 'Request denied!');

    }
}
