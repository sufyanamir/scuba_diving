<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Customers;
use App\Models\ServiceOverheads;
use App\Models\ServiceRequests;
use App\Models\Services;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class ApiController extends Controller
{

    //----------------------------------------------------Service APIs------------------------------------------------------//
    //get service detail
    public function getServicedetail(Request $request)
    {
        try {
            $user = Auth::user();
            $serviceId = $request->input('serviceId'); // Get the 'serviceId' parameter from the request

            // Start by retrieving all services that belong to the user's company
            $query = Services::where('company_id', $user->company_id);

            // If a 'serviceId' parameter is provided, filter services by 'service_name'
            if (!empty($serviceId)) {
                $query->where('service_id', $serviceId);
            }

            // Retrieve the filtered services
            $services = $query->get();

            // Initialize an empty array to store the final response data
            $responseData = [];

            // Retrieve all data from the 'service_overheads' table
            $allServiceOverheads = ServiceOverheads::all();

            // Iterate through each service to fetch its associated overheads
            foreach ($services as $service) {
                $serviceId = $service->service_id;

                // Filter service overheads by service_id
                $overheads = $allServiceOverheads->where('service_id', $serviceId)->toArray();

                // Calculate the total overhead cost for this service
                $totalCost = array_sum(array_column($overheads, 'overhead_cost'));

                // Add 'service_overheads' and 'total_overhead_cost' to the 'service' object
                $service->service_overheads = array_values($overheads); // Re-index the array
                // $service->total_overhead_cost = $totalCost;

                // Add the service data to the response array
                $responseData[] = $service;
            }

            if (!empty($responseData)) {
                return response()->json(['success' => true, 'data' => ['services' => $responseData]], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'No services found!'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    //get service detail

    //get service
    public function getService(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->input('search'); // Get the 'search' parameter from the request

            // Start by retrieving all services that belong to the user's company
            $query = Services::where('company_id', $user->company_id);

            // If a 'search' parameter is provided, filter services by 'service_name'
            if (!empty($search)) {
                $query->where('service_name', 'like', '%' . $search . '%');
            }

            // Retrieve the filtered services
            $services = $query->get();

            // Initialize an empty array to store the final response data
            $responseData = [];

            // Retrieve all data from the 'service_overheads' table
            $allServiceOverheads = ServiceOverheads::all();

            // Iterate through each service to fetch its associated overheads
            foreach ($services as $service) {
                $serviceId = $service->service_id;

                // Filter service overheads by service_id
                $overheads = $allServiceOverheads->where('service_id', $serviceId)->toArray();

                // Calculate the total overhead cost for this service
                $totalCost = array_sum(array_column($overheads, 'overhead_cost'));

                // Add 'service_overheads' and 'total_overhead_cost' to the 'service' object
                $service->service_overheads = array_values($overheads); // Re-index the array
                // $service->total_overhead_cost = $totalCost;

                // Add the service data to the response array
                $responseData[] = $service;
            }

            if (!empty($responseData)) {
                return response()->json(['success' => true, 'data' => ['services' => $responseData]], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'No services found!'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }


    //get service

    //delete service
    public function deleteService($id)
    {
        try {
            $service = Services::find($id);

            if (!$service) {
                return response()->json(['success' => false, 'message' => 'No services found!'], 404);
            }

            // Delete associated overheads.
            $service->overheads()->delete();

            $path = 'storage/service_images/' . $service->service_image;

            if (File::exists($path)) {
                File::delete($path);
            }

            $service->delete();

            return response()->json(['success' => true, 'message' => 'Service deleted successfully!'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }
    }
    //delete service

    //update service
    public function updateService(Request $request, $id)
    {
        try {
            // Validate the form data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'subtitle' => 'required|string|max:255',
                'charges' => 'required|numeric',
                'description' => 'required|string|max:400',
                'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                // 'added_user_id' => 'required',
                // 'company_id' => 'required',
                'overheads' => 'array', // Define 'overheads' as an array
                // 'overheads.*.cost_name' => 'required|string|max:255',
                // 'overheads.*.cost' => 'required|numeric',
            ]);

            // Find the service to be updated
            $service = Services::find($id);

            if (!$service) {
                return response()->json(['success' => false, 'message' => 'No service found!'], 404);
            }
            $user = Auth::user();
            // Update the service data
            $service->service_name = $validatedData['name'];
            $service->service_subtitle = $validatedData['subtitle'];
            $service->service_charges = $validatedData['charges'];
            $service->service_desc = $validatedData['description'];
            $service->added_user_id = $user->id;
            $service->company_id = $user->company_id;

            // Upload and store the updated service image
            if ($request->hasFile('upload_image')) {
                $image = $request->file('upload_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/service_images', $imageName); // Adjust storage path as needed
                $service->service_image = $imageName;
            }

            $service->save();

            // Delete existing overhead data for the service
            ServiceOverheads::where('service_id', $id)->delete();

            // Insert updated overhead data into the 'services_overheads' table for the service
            if (!empty($request['cost_name'])) {
                $overheads = $request['cost_name'];
                $count = count($overheads);

                for ($i = 0; $i < $count; $i++) {
                    ServiceOverheads::create([
                        'service_id' => $service->service_id,
                        'overhead_name' => $_REQUEST['cost_name'][$i],
                        'overhead_cost' => $_REQUEST['cost'][$i],
                    ]);
                }
            }

            // Optionally, you can redirect back with a success message
            return response()->json(['success' => true, 'message' => 'Service updated successfully!']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    //update service


    //add service
    public function addService(Request $request)
    {

        try {
            // Validate the form data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'subtitle' => 'required|string|max:255',
                'charges' => 'required|numeric',
                'description' => 'required|string|max:400',
                'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                'cost_name' => 'array',
                'cost' => 'array',
                // 'added_user_id' => 'required',
                // 'company_id' => 'required',
                // 'overheads' => 'array', // Define 'overheads' as an array
            ]);

            $user = Auth::user();
            // Insert data into the 'services' table
            $service = new Services([
                'service_name' => $validatedData['name'],
                'service_subtitle' => $validatedData['subtitle'],
                'service_charges' => $validatedData['charges'],
                'service_desc' => $validatedData['description'],
                'added_user_id' => $user->id,
                'company_id' => $user->company_id,
            ]);

            // Upload and store the service image if it exists
            if ($request->hasFile('upload_image')) {
                $image = $request->file('upload_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/service_images', $imageName); // Adjust storage path as needed
                $service->service_image = $imageName;
            }

            $service->save();

            // Insert overhead data into the 'services_overheads' table for the service
            if (!empty($validatedData['cost_name'])) {
                $overheads = $validatedData['cost_name'];
                $count = count($overheads);
            
                for ($i = 0; $i < $count; $i++) {
                    ServiceOverheads::create([
                        'service_id' => $service->service_id,
                        'overhead_name' => $overheads[$i],
                        'overhead_cost' => $validatedData['cost'][$i],
                    ]);
                }
            }

            // Optionally, you can redirect back with a success message
            return response()->json(['success' => true, 'message' => 'Service added successfully!']);
        } catch (\Exception $e) {

            return response()->json(['success' => true, 'message' => $e->getMessage()], 400);
        }
    }
    //add service

    //----------------------------------------------------Service APIs------------------------------------------------------//

    //----------------------------------------------------Staff APIs------------------------------------------------------//
    //get staff detail
    public function getStaffDetail(Request $request)
    {
        try {
            $user = Auth::user();
            $StaffId = $request->input('staffId'); // Get the 'search' parameter from the request

            $query = User::where('user_role', '2')->where('company_id', $user->company_id);

            // If a 'search' parameter is provided, filter staff by user name
            if (!empty($StaffId)) {
                $query->where('id', $StaffId);
            }

            $staff = $query->first();

            if ($staff->count() > 0) {
                return response()->json(['success' => true, 'data' => ['staff' => $staff]], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'No staff found!'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    //get staff detail
    //get staff
    public function getStaff(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->input('search'); // Get the 'search' parameter from the request

            $query = User::where('user_role', '2')->where('company_id', $user->company_id);

            // If a 'search' parameter is provided, filter staff by user name
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            $staff = $query->select('id', 'name', 'category as role', 'user_image')->get();

            if ($staff->count() > 0) {
                return response()->json(['success' => true, 'data' => ['staff' => $staff]], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'No staff found!'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    //get staff

    //delete staff
    public function deleteStaff($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'No staff found!'], 404);
            }

            $path = 'storage/staff_images/' . $user->user_image;
            if (File::exists($path)) {

                File::delete($path);
            }
            $user->delete();

            return response()->json(['success' => true, 'message' => 'Staff deleted successfully!'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //delete staff

    //update staff
    public function updateStaff(Request $request, $id)
    {

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'No staff found!'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|regex:/^[0-9]+$/|max:20',
                'address' => 'required|string|max:400',
                'category' => 'required|string|max:400',
                'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                // 'company_id' => 'required',
                // Add more validation rules for other fields
            ]);
            if ($request->hasFile('upload_image')) {

                $path = 'public/staff_images/' . $user->user_image;
                // dd($path);
                if ($path) {
                    Storage::delete($path);
                }

                $image = $request->file('upload_image');
                $ext = $image->getClientOriginalExtension();
                $imageName = time() . "." . $ext;
                $image->storeAs('public/staff_images', $imageName);
                $user->user_image = $imageName;
            }


            $fbAcc = $request->input('fb_acc');
            $igAcc = $request->input('ig_acc');
            $ttAcc = $request->input('tt_acc');

            $socailLinks = "$fbAcc,$igAcc,$ttAcc";


            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->phone = $validatedData['phone'];
            $user->address = $validatedData['address'];
            $user->category = $validatedData['category'];
            $user->company_id = $user->company_id;
            $user->social_links = $socailLinks;
            $user->update();

            return response()->json(['success' => true, 'message' => 'Staff updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //update staff

    //add staff
    public function addStaff(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'phone' => 'required|regex:/^[0-9]+$/|max:20',
                'address' => 'required|string|max:400',
                'category' => 'required|string|max:400',
                'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                // 'company_id' => 'required',
                // Add more validation rules for other fields
            ]);
            $fbAcc = $request->input('fb_acc');
            $igAcc = $request->input('ig_acc');
            $ttAcc = $request->input('tt_acc');

            $socailLinks = "$fbAcc,$igAcc,$ttAcc";
            $user = Auth::user();
            $dataToInsert = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'],
                'category' => $validatedData['category'],
                'company_id' => $user->company_id,
                'social_links' => $socailLinks,
                'user_role' => '2',
                // Add other fields as needed
            ];

            if (!empty($validatedData['upload_image'])) {
                $dataToInsert['user_image'] = $validatedData['upload_image'];
            }

            DB::table('users')->insert($dataToInsert);


            if ($request->hasFile('upload_image')) {
                // Get the uploaded file
                $image = $request->file('upload_image');

                // Generate a unique name for the image
                $imageName = time() . '.' . $image->getClientOriginalExtension();

                // Store the image in the specified storage location
                $image->storeAs('public/staff_images', $imageName); // Adjust storage path as needed

                // Now, if you want to associate the uploaded image filename with the inserted record, you would need to retrieve the last inserted ID.
                $lastInsertedId = DB::getPdo()->lastInsertId();

                // Update the 'upload_image' field for the inserted record
                DB::table('users')
                    ->where('id', $lastInsertedId)
                    ->update(['user_image' => $imageName]);
            }

            // Optionally, you can redirect back with a success message
            return response()->json(['success' => true, 'message' => 'Staff added successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //add staff

    //----------------------------------------------------Staff APIs------------------------------------------------------//

    //----------------------------------------------------Customer APIs------------------------------------------------------//
    //get customer detail
    public function getCustomerDetail(Request $request)
    {
        $user = Auth::user();
        $customerId = $request->input('customerId'); // Get the 'name' parameter from the request
        // Define a mapping of status labels to their numeric values
        $statusMapping = [
            'new' => 0,
            'active' => 1,
            'pending' => 2,
            'completed' => 3,
        ];
        try {
            $query = Customers::where('company_id', $user->company_id);

            if (!empty($customerId)) {
                $query->where('customer_id', $customerId);
            }

            $customers = $query->get();

            if ($customers->count() > 0) {
                $customerData = $customers->map(function ($customer) use ($statusMapping) {
                    // Map numeric status back to status labels
                    $customer->customer_status = array_search($customer->customer_status, $statusMapping);
                    return $customer;
                })->first();
                return response()->json(['success' => true, 'data' => ['customer' => $customerData]], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'No customers found!'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //get customer detail

    //get customer
    public function getCustomer(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search'); // Get the 'name' parameter from the request
        $statusFilter = $request->input('status'); // Get the 'status' parameter from the request

        // Define a mapping of status labels to their numeric values
        $statusMapping = [
            'new' => 0,
            'active' => 1,
            'pending' => 2,
            'completed' => 3,
        ];

        try {
            $query = Customers::where('company_id', $user->company_id);

            if (!empty($search)) {
                $query->where('customer_name', 'like', '%' . $search . '%');
            }

            if (!empty($statusFilter)) {
                if ($statusFilter === 'all') {
                }
                // Check if the provided status exists in the mapping
                elseif (isset($statusMapping[$statusFilter])) {
                    $numericStatus = $statusMapping[$statusFilter];
                    // Add status filtering to the query
                    $query->where('customer_status', $numericStatus);
                } else {
                    return response()->json(['success' => false, 'message' => 'Invalid status filter.'], 400);
                }
            }

            $customers = $query->select('customer_id', 'customer_name', 'customer_email', 'customer_image', 'customer_status')->get();

            if ($customers->count() > 0) {
                $customerData = $customers->map(function ($customer) use ($statusMapping) {
                    // Map numeric status back to status labels
                    $customer->customer_status = array_search($customer->customer_status, $statusMapping);
                    return $customer;
                });

                return response()->json(['success' => true, 'data' => ['customers' => $customerData]], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'No customers found!'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    //get customer
    //delete customer
    public function deleteCustomer($id)
    {
        try {
            $user = Customers::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'No customer found!'], 404);
            }

            $path = 'storage/customer_images/' . $user->customer_image;
            if (File::exists($path)) {

                File::delete($path);
            }
            $user->delete();

            return response()->json(['success' => true, 'message' => 'Customer deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //delete customer

    //add customer
    public function addCustomer(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:customers,customer_email',
                'phone' => 'required|regex:/^[0-9]+$/|max:20',
                'address' => 'required|string|max:400',
                'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                // 'company_id' => 'required',
                // 'added_user_id' => 'required',
                // Add more validation rules for other fields
            ]);
            $fbAcc = $request->input('fb_acc');
            $igAcc = $request->input('ig_acc');
            $ttAcc = $request->input('tt_acc');

            $status = 0;

            $socailLinks = "$fbAcc,$igAcc,$ttAcc";
            $user = Auth::user();
            $dataToInsert = [
                'customer_name' => $validatedData['name'],
                'customer_email' => $validatedData['email'],
                'customer_phone' => $validatedData['phone'],
                'customer_address' => $validatedData['address'],
                'company_id' => $user->company_id,
                'added_user_id' => $user->id,
                'customer_social_links' => $socailLinks,
                'customer_status' => $status,
                // Add other fields as needed
            ];

            if (!empty($validatedData['upload_image'])) {
                $dataToInsert['customer_image'] = $validatedData['upload_image'];
            }

            DB::table('customers')->insert($dataToInsert);

            if ($request->hasFile('upload_image')) {
                // Get the uploaded file
                $image = $request->file('upload_image');

                // Generate a unique name for the image
                $imageName = time() . '.' . $image->getClientOriginalExtension();

                // Store the image in the specified storage location
                $image->storeAs('public/customer_images', $imageName); // Adjust storage path as needed

                // Now, if you want to associate the uploaded image filename with the inserted record, you would need to retrieve the last inserted ID.
                $lastInsertedId = DB::getPdo()->lastInsertId();

                // Update the 'upload_image' field for the inserted record
                DB::table('customers')
                    ->where('customer_id', $lastInsertedId)
                    ->update(['customer_image' => $imageName]);
            }

            // Optionally, you can redirect back with a success message
            return response()->json(['success' => true, 'message' => 'Customer added successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //add customer

    //updating customer
    public function updateCustomer(Request $request, $id)
    {
        try {
            $user = Customers::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'No customer found!'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|regex:/^[0-9]+$/|max:20',
                'address' => 'required|string|max:400',
                'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                // Add more validation rules for other fields
            ]);
            if ($request->hasFile('upload_image')) {

                $path = 'public/customer_images/' . $user->customer_image;
                // dd($path);
                if ($path) {
                    Storage::delete($path);
                }

                $image = $request->file('upload_image');
                $ext = $image->getClientOriginalExtension();
                $imageName = time() . "." . $ext;
                $image->storeAs('public/customer_images', $imageName);
                $user->customer_image = $imageName;
            }


            $fbAcc = $request->input('fb_acc');
            $igAcc = $request->input('ig_acc');
            $ttAcc = $request->input('tt_acc');

            $socailLinks = "$fbAcc,$igAcc,$ttAcc";


            $user->customer_name = $validatedData['name'];
            $user->customer_email = $validatedData['email'];
            $user->customer_phone = $validatedData['phone'];
            $user->customer_address = $validatedData['address'];
            $user->customer_social_links = $socailLinks;
            $user->update();

            return response()->json(['success' => true, 'message' => 'Customer updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //updating customer

    //----------------------------------------------------Customer APIs------------------------------------------------------//

    //getting dashboard
    public function adminDashboard()
    {
        try {

            $user = Auth::user();

            $data = Customers::where('company_id', $user->company_id)->get();

            $totalCustomers = Customers::where('company_id', $user->company_id)->count();

            if ($data->count() > 0) {
                return response()->json(['success' => true, 'data' => ['cutomers' => $data, 'totalCustomers' => $totalCustomers]], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'No records found'], 404);
            }
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //getting dashboard

    //----------------------------------------------------Authentication APIs------------------------------------------------------//

    //login
    public function login(Request $request)
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');

            $user = User::where('email', $email)->first();

            if (!$user || md5($password) !== $user->password) {
                return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
            }

            $userRole = $user->user_role;
            if ($userRole != 1 && $userRole != 2) {
                // User role is not allowed to login
                return response()->json(['message' => 'User role not allowed to login'], 401);
            }

            // Generate a personal access token for the user
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json(['success' => true, 'message' => 'Login successful!', 'access_token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //login
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            // Revoke the user's token(s)
            $user->tokens()->delete();

            return response()->json(['success' => true, 'message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    //request for a service
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
            // Handle other exceptions, such as database messages
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //request for a service

    //----------------------------------------------------Authentication APIs------------------------------------------------------//

    //----------------------------------------------------User APIs------------------------------------------------------//
    //get user detail
    public function getUserDetails(Request $request)
    {
        $user = $request->user(); // This will give you the authenticated user
        return response()->json(['success' => true, 'data' => ['user_details' => $user]], 200);
    }
    //get user detail

    //update user detail
    public function updateUserDetail(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|regex:/^[0-9]+$/|max:20',
                'password' => 'nullable',
                'address' => 'nullable|string|max:400',
                'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                'role' => 'required|in:1,2',
            ]);

            $folder = ($request->role == 1) ? 'company_images' : 'staff_images';

            if ($request->hasFile('upload_image')) {

                if ($user->user_image) {
                    Storage::delete($user->user_image);
                }

                $imagePath = $request->file('upload_image')->store('public/' . $folder);
                $user->user_image = $imagePath;
            }

            // Conditionally update user attributes if they are not null
            if ($validatedData['name'] !== null) {
                $user->name = $validatedData['name'];
            }

            if ($validatedData['phone'] !== null) {
                $user->phone = $validatedData['phone'];
            }

            if ($validatedData['password'] !== null) {
                $user->password = md5($validatedData['password']);
            }

            if ($validatedData['address'] !== null) {
                $user->address = $validatedData['address'];
            }

            $user->update();

            return response()->json(['success' => true, 'message' => 'Data updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    //update user detail
    //----------------------------------------------------User APIs------------------------------------------------------//

}
