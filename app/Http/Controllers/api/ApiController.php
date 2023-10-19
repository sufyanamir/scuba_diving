<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\forgotPasswordMail;
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
use App\Models\Orders;
use App\Models\OrderItems;
use App\Models\AdditionalItems;
use App\Models\Company;
use App\Models\imageGallery;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Unique;

class ApiController extends Controller
{
    protected $appUrl = 'https://scubadiving.thewebconcept.tech/';
    //----------------------------------------------------Image APIs------------------------------------------------------//
    //get feed
    public function getFeed(Request $request)
    {
        $user = Auth::user();
        try {
            $companyId = $user->company_id;

            $feed = imageGallery::where('company_id', $companyId)->inRandomOrder()->take(20)->get();

            $feed = $feed->map(function ($item) {
                $item['customer_name'] = Customers::where('customer_id', $item->customer_id)->value('customer_name');
                $item['staff_name'] = User::where('id', $item->staff_id)->value('name');
                return $item;
            });
            if (!$feed) {
                return response()->json(['success' => false, 'message' => 'No data found in the feed!'], 404);
            }

            return response()->json(['success' => true, 'data' => ['feed' => $feed]], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //get feed

    //post image
    public function getMedia(Request $request)
    {
        $user = Auth::user();
        try {
            $companyId = $user->company_id;

            $customerId = $request->input('customerId');
            $images = imageGallery::where(['customer_id' => $customerId, 'company_id' => $companyId])->get();

            if ($images->count() === 0) {
                return response()->json(['success' => false, 'message' => 'No media found of this customer'], 404);
            }

            return response()->json(['success' => true, 'data' => ['media' => $images]], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //post image

    //post image
    public function postMedia(Request $request)
    {
        $user = Auth::user();

        try {
            $validatedData = $request->validate([
                'staff_id' => 'nullable|numeric',
                'customer_id' => 'required|numeric',
                'order_id' => 'nullable|numeric',
                'media_type' => 'required|string',
                'upload_media' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi', // Allow any size for videos
            ]);

            $postMedia = new imageGallery([
                'customer_id' => $validatedData['customer_id'],
                'company_id' => $user->company_id,
                'order_id' => $validatedData['order_id'],
                'staff_id' => $validatedData['staff_id'],
                'media_type' => $validatedData['media_type'],
                'added_user_id' => $user->id,
                'app_url' => $this->appUrl,
            ]);

            if ($request->hasFile('upload_media')) {
                $media = $request->file('upload_media');
                $mediaExtension = $media->getClientOriginalExtension();
                $mediaName = time() . '.' . $mediaExtension;
                $storagePath = $mediaExtension === 'mp4' || $mediaExtension === 'mov' || $mediaExtension === 'avi' ?
                    'public/video_gallery' : 'public/image_gallery';

                $media->storeAs($storagePath, $mediaName);

                $postMedia->stored_media = 'storage/' . ($mediaExtension === 'mp4' || $mediaExtension === 'mov' || $mediaExtension === 'avi' ?
                    'video_gallery' : 'image_gallery') . '/' . $mediaName;
            }

            $postMedia->save();

            return response()->json(['success' => true, 'message' => 'Media uploaded successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //post image
    //----------------------------------------------------Image APIs------------------------------------------------------//

    //----------------------------------------------------Order APIs------------------------------------------------------//

    public function getOrderDetails(Request $request)
    {
        try {
            $order_id = $request->input('orderId');
            $statusMapping = [
                'new' => 0,
                'active' => 1,
                'pending' => 2,
                'completed' => 3,
                'paid' => 4,
            ];
            // Find the order by order_id and eager load its relationships including the customer
            $order = Orders::with('customer')->find($order_id);

            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }
            $order->start_date = Carbon::parse($order->start_date)->format('d M Y');
            $order->end_date = Carbon::parse($order->end_date)->format('d M Y');
            // Get the order items for the order
            $orderItems = OrderItems::where('order_id', $order->order_id)->get();

            // Create a new array to hold the modified data
            $modifiedOrderItems = [];

            foreach ($orderItems as $orderItem) {
                $service = Services::find($orderItem->service_id);
                if ($service) {
                    $serviceData = [
                        'service_id' => $service->service_id,
                        'service_name' => $service->service_name,
                        'service_subtitle' => $service->service_subtitle,
                        'service_charges' => $service->service_charges,
                        'service_desc' => $service->service_desc,
                        'service_image' => $service->service_image,
                        'added_user_id' => $service->added_user_id,
                        'company_id' => $service->company_id,
                        'service_duration' => $service->service_duration,
                        'order_item_qty' => $orderItem->order_item_qty,
                    ];

                    $modifiedOrderItems[] = $serviceData;
                }
            }

            // Convert order_status and customer_status to strings based on the $statusMapping
            $order->setAttribute('order_status', array_search($order->order_status, $statusMapping));
            $order->customer->setAttribute('customer_status', array_search($order->customer->customer_status, $statusMapping));

            // Assign the modified data to the order_items property
            $order->setAttribute('order_items', $modifiedOrderItems);

            return response()->json(['success' => true, 'data' => $order], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }


    //get orders
    public function getOrders(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->input('search');

            $query = Orders::select('orders.order_id', 'orders.order_total', 'customers.customer_name', 'customers.customer_image')
                ->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
                ->where('orders.company_id', $user->company_id);

            if (!empty($search)) {
                $query->where('customers.customer_name', 'like', '%' . $search . '%');
            }
            $orders = $query->orderBy('order_id', 'desc')->get();

            $responseData = $orders;

            if (!empty($responseData)) {
                return response()->json(['success' => true, 'data' => ['orders' => $responseData]], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'No record found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //get orders

    //create order
    public function createOrder(Request $request)
    {
        try {
            $statusMapping = [
                'new' => 0,
                'active' => 1,
                'pending' => 2,
                'completed' => 3,
                'paid' => 4,
            ];
            $user = Auth::user();
            // Validate the incoming JSON data
            $validatedData = $request->validate([
                'customer_id' => 'required|integer',
                'services' => 'required|array',
                'services.*.service_id' => 'required|integer',
                'services.*.qty' => 'required|integer',
                'start_Date' => 'required',
                'end_Date' => 'required',
                'additional_cost_list' => 'nullable|array',
                'additional_cost_list.*.serviceName' => 'nullable|string',
                'additional_cost_list.*.serviceCost' => 'nullable|numeric',
                'discount' => 'required|numeric',
                'remarks' => 'nullable|string',
                'total' => 'required|numeric',
                'invoice_status' => 'required|in:pending,paid',
                'additional_cost_total' => 'required|numeric',
                'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:1024',
                'total_duration' => 'required|numeric',
                'sub_total' => 'required|numeric',
            ]);

            $status = $statusMapping[$validatedData['invoice_status']];

            $customer = Customers::find($validatedData['customer_id']);

            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
            }

            // Upload and save the image if provided
            $uploadedImage = $request->file('upload_image');
            $imagePath = null; // Initialize the image path

            if ($uploadedImage) {
                $imageName = Str::random(20) . '.' . $uploadedImage->getClientOriginalExtension();

                // Generate the full URL of the uploaded image
                $imageUrl = url('storage/payment_receipts/' . $imageName);

                // Store the image URL in the database
                $imagePath = $imageUrl;
            }

            // Create an order record
            $order = Orders::create([
                'customer_id' => $validatedData['customer_id'],
                'start_date' => date($validatedData['start_Date']),
                'end_date' => date($validatedData['end_Date']),
                'order_additional_items_total' => $validatedData['additional_cost_total'],
                'order_discount' => $validatedData['discount'],
                'order_remarks' => $validatedData['remarks'],
                'order_total' => $validatedData['total'],
                'order_status' => $status,
                'payment_receipt_path' => $imagePath,
                'company_id' => $user->company_id,
                'total_duration' => $validatedData['total_duration'],
                'sub_total' => $validatedData['sub_total'],
            ]);


            // Create order items
            foreach ($validatedData['services'] as $service) {
                OrderItems::create([
                    'order_id' => $order->order_id,
                    'service_id' => $service['service_id'],
                    'order_item_qty' => $service['qty'],
                ]);
            }

            // Create additional items
            foreach ($validatedData['additional_cost_list'] as $additionalItem) {
                AdditionalItems::create([
                    'order_id' => $order->order_id,
                    'additional_item_name' => $additionalItem['serviceName'],
                    'additional_item_cost' => $additionalItem['serviceCost'],
                ]);
            }

            if ($customer) {
                if ($status == 4) {
                    $customer->customer_status = 1;
                } else {
                    $customer->customer_status = 2;
                }

                $customer->save();
            }

            return response()->json(['success' => true, 'message' => 'Order created successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //create order
    //----------------------------------------------------Order APIs------------------------------------------------------//

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
            $query->orderBy('service_id', 'desc');
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
                'service_duration' => 'required|numeric',
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
            $service->service_duration = $validatedData['service_duration'];
            $service->added_user_id = $user->id;
            $service->company_id = $user->company_id;
            $service->app_url = $this->appUrl;

            // Upload and store the updated service image
            if ($request->hasFile('upload_image')) {
                $image = $request->file('upload_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/service_images', $imageName); // Adjust storage path as needed
                $service->service_image = 'storage/service_images/' . $imageName;
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
            $user = Auth::user();

            // Validate the incoming JSON data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'subtitle' => 'required|string|max:255',
                'charges' => 'required|numeric',
                'description' => 'required|string|max:400',
                'service_duration' => 'required|numeric',
                'servicesOverheads' => 'required|array',
                'servicesOverheads.*.cost_name' => 'nullable|string',
                'servicesOverheads.*.cost' => 'nullable|numeric',
                'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            ]);
            $existingService = Services::where('service_name', $validatedData['name'])
                ->where('company_id', $user->company_id)
                ->first();

            if ($existingService) {
                return response()->json(['success' => false, 'message' => 'Service with the same name already exists'], 400);
            }
            // Create a new service
            $service = new Services([
                'service_name' => $validatedData['name'],
                'service_subtitle' => $validatedData['subtitle'],
                'service_charges' => $validatedData['charges'],
                'service_desc' => $validatedData['description'],
                'service_duration' => $validatedData['service_duration'],
                'added_user_id' => $user->id,
                'company_id' => $user->company_id,
                'app_url' => $this->appUrl,
            ]);

            // Upload and store the service image if it exists
            if ($request->hasFile('upload_image')) {
                $image = $request->file('upload_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/service_images', $imageName); // Adjust storage path as needed
                $service->service_image = 'storage/service_images/' . $imageName;
            }

            $service->save();

            // Insert overhead data into the 'services_overheads' table for the service
            if (!empty($validatedData['servicesOverheads'])) {
                foreach ($validatedData['servicesOverheads'] as $overhead) {
                    ServiceOverheads::create([
                        'service_id' => $service->service_id,
                        'overhead_name' => $overhead['cost_name'],
                        'overhead_cost' => $overhead['cost'],
                    ]);
                }
            }

            // Return a success response
            return response()->json(['success' => true, 'message' => 'Service added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
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
            $staffId = $request->input('staffId');
            $includeCustomers = $request->input('includeCustomers', false);

            $query = User::where('user_role', '2')->where('company_id', $user->company_id);

            // If a 'search' parameter is provided, filter staff by user name
            if (!empty($staffId)) {
                $query->where('id', $staffId);
            }

            $staff = $query->first();

            $response = ['success' => true, 'data' => ['staff' => $staff]];

            if ($includeCustomers) {
                $customers = Customers::where('staff_id', $staffId)->get();
                $response['data']['customers'] = $customers;
            }

            if ($staff->count() > 0) {
                return response()->json($response, 200);
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
            $query->orderBy('id', 'desc');

            $staff = $query->select('id', 'name', 'category as role', 'user_image', 'app_url')->get();

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
                $user->user_image = 'storage/staff_images/' . $imageName;
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
            $user->app_url = $this->appUrl;
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
                'app_url' => $this->appUrl,
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
                    ->update(['user_image' => 'storage/staff_images/' . $imageName]);
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
    //assign customer
    public function assignCustomer(Request $request)
    {
        $user = Auth::user();
        try {
            $validatedData = $request->validate([
                'staff_id' => 'required|numeric',
                'customer_id' => 'required|numeric',
            ]);

            $staffId = $validatedData['staff_id'];
            $customerId = $validatedData['customer_id'];

            $customer = Customers::where('customer_id', $customerId)->where('company_id', $user->company_id)->whereIn('customer_status', [1, 2])->first();
            $staff = User::where('id', $staffId)->where('user_role', '2')->where('company_id', $user->company_id)->first();

            if (!$staff) {
                return response()->json(['success' => false, 'message' => 'staff not found!'], 404);
            } elseif (!$customer) {
                return response()->json(['success' => false, 'message' => 'customer not found!'], 404);
            }

            if ($customer->customer_assigned !== 0) {
                return response()->json(['success' => false, 'message' => 'Customer is already assigned to a staff.'], 400);
            }

            $customer->staff_id = $staffId;
            $customer->customer_assigned = true;
            $customer->save();

            return response()->json(['success' => true, 'message' => 'The customer has assigned to the staff'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //assign customer

    //get customer detail
    public function getCustomerDetail(Request $request)
    {
        $user = Auth::user();
        $customerId = $request->input('customerId');
        $includeOrders = $request->input('includeOrders', false); // Add a new parameter for including orders

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

            $customer = $query->first();

            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
            }

            // Map numeric status back to status labels
            $customer->customer_status = array_search($customer->customer_status, $statusMapping);

            $response = ['success' => true, 'data' => ['customer' => $customer]];

            if ($includeOrders) {
                // Retrieve and include the customer's orders
                $orders = Orders::where('customer_id', $customer->customer_id)->orderBy('order_id', 'desc')->get();
                $response['data']['orders'] = $orders;
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //get customer detail

    //get customer
    //get customer
    public function getCustomer(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $assignCustomers = $request->input('assignCustomers');

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

            $query->orderBy('customer_id', 'desc');

            // Map the user_role string to its numeric value (1 for admin, 2 for staff)
            $userRoleMapping = [
                'admin' => 1,
                'staff' => 2,
            ];

            if ($user->user_role == 2) {
                // If the user has 'staff' role (user_role 2), filter by staff_id
                $query->where('staff_id', $user->id);
            }

            if ($assignCustomers === 'true') {
                $query->whereIn('customer_status', [1, 2]);
            } else {
                if (!empty($statusFilter)) {
                    if ($statusFilter === 'all') {
                    } elseif (isset($statusMapping[$statusFilter])) {
                        $numericStatus = $statusMapping[$statusFilter];
                        $query->where('customer_status', $numericStatus);
                    } else {
                        return response()->json(['success' => false, 'message' => 'Invalid status filter.'], 400);
                    }
                }
            }

            $customers = $query->select('customer_id', 'customer_name', 'customer_email', 'customer_image', 'app_url', 'customer_status', 'customer_assigned')->get();

            if ($customers->count() > 0) {
                $customerData = $customers->map(function ($customer) use ($statusMapping) {
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
                'app_url' => $this->appUrl,
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
                    ->update(['customer_image' => 'storage/customer_images/' . $imageName]);
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
            $user->app_url = $this->appUrl;
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

    //forgot password
    public function forgotPassword(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|string',
            ]);
            $email = $validatedData['email'];

            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'The user does not exist.'], 400);
            }

            $otp = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $emailData = [
                'name' => $user->name,
                'otp' => $otp,
            ];

            $mail = new forgotPasswordMail($emailData);
            try {
                Mail::to($email)->send($mail);
                $user->otp = $otp;
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            $user->save();

            return response()->json(['success' => true, 'message' => 'Please check your mail for the otp.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //forgot password

    //validate otp
    public function validateOtp(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'otp' => 'required|integer',
            ]);
            $otp = $validatedData['otp'];

            $otpCheck = User::where('otp', $otp)->first();

            if (!$otpCheck) {
                return response()->json(['success' => false, 'message' => 'Provided otp is incorrect!'], 400);
            }

            return response()->json(['success' => true, 'message' => 'Otp is correct! Now you can reset your password.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //validate otp

    //reset password
    public function resetPassword(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'otp' => 'required|integer',
                'new_password' => 'required'
            ]);

            $otp = $validatedData['otp'];
            $password = md5($validatedData['new_password']);

            $user = User::where('otp', $otp)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User did not found against the provided otp!'], 404);
            }

            $user->password = $password;

            $user->save();
            $user->update(['otp' => null]);

            return response()->json(['success' => true, 'message' => 'Your password is successfully changed. Now! you can login with your new password'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    //reset password

    //----------------------------------------------------Authentication APIs------------------------------------------------------//

    //----------------------------------------------------User APIs------------------------------------------------------//
    //get user detail
    public function getUserDetails(Request $request)
    {
        $user = $request->user(); // This will give you the authenticated user

        $userRoleLabel = ($user->user_role == 1) ? 'admin' : ($user->user_role == 2 ? 'staff' : 'unknown');

        // Update the user_role field with the label
        $user->user_role = $userRoleLabel;

        $company = Company::find($user->company_id);
        $user->company_name = $company ? $company->company_name : 'Unknown company';
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
            ]);

            $folder = ($user->user_role == 1) ? 'company_images' : 'staff_images';

            if ($request->hasFile('upload_image')) {

                if ($user->user_image) {
                    // Remove the 'public/' prefix to store only the relative path
                    Storage::delete($user->user_image);
                }

                $imagePath = $request->file('upload_image')->store("public/$folder");

                // Update the user_image field with the completse path
                $user->user_image = str_replace('public/', 'storage/' . $folder . '/', $imagePath);
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

            // Save the updated user data to the database
            $user->save();

            return response()->json(['success' => true, 'message' => 'Data updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    //update user detail
    //----------------------------------------------------User APIs------------------------------------------------------//

}
