<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Services;
use App\Models\ServiceOverheads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ServicesController extends Controller
{
    public function destroy($id)
    {
        $service = Services::find($id);

        if (!$service) {
            return redirect('services')->with('error', 'Service not found');
        }

        // Delete associated overheads.
        $service->overheads()->delete();

        $path = 'storage/service_images/' . $service->service_image;

        if (File::exists($path)) {
            File::delete($path);
        }

        $service->delete();

        return redirect('services')->with('status', 'Service deleted successfully');
    }

    public function index()
    {
        // Retrieve all services along with their associated service_overheads
        $services = Services::with('serviceOverheads')->get();

        // Calculate the sum of overhead_cost for each service using DB::raw
        $totalOverheadCosts = ServiceOverheads::select('service_id', DB::raw('SUM(overhead_cost) as total_cost'))
            ->groupBy('service_id')
            ->get()
            ->keyBy('service_id');

        // Retrieve user details from the session
        $user_details = session('user_details');

        return view('services', [
            'services' => $services,
            'totalOverheadCosts' => $totalOverheadCosts, // Pass the calculated sums to the view
            'userDetails' => $user_details,
        ]);
    }

    public function addService(Request $request)
    {
        //dd($request);
        // Validate the form data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'charges' => 'required|numeric',
            'description' => 'required|string|max:500',
            'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'overheads' => 'array', // Define 'overheads' as an array
            // 'overheads.*.cost_name' => 'required|string|max:255',
            // 'overheads.*.cost' => 'required|numeric',
        ]);

        // Insert data into the 'services' table
        $service = new Services([
            'service_name' => $validatedData['name'],
            'service_subtitle' => $validatedData['subtitle'],
            'service_charges' => $validatedData['charges'],
            'service_desc' => $validatedData['description'],
        ]);

        // Upload and store the service image
        if ($request->hasFile('upload_image')) {
            $image = $request->file('upload_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/service_images', $imageName); // Adjust storage path as needed
            $service->service_image = $imageName;
        }

        $service->save();

        // Insert overhead data into the 'services_overheads' table for the service
        // Insert overhead data into the 'services_overheads' table for the service
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
            // foreach ($validatedData['overheads'] as $overheadData) {
            //     // dd($overheadData);
            //     ServiceOverheads::create([
            //         'service_id' => $service->id,
            //         'overhead_name' => $overheadData['cost_name'],
            //         'overhead_cost' => $overheadData['cost'],
            //     ]);
            // }
            // dd($_REQUEST);
        } else {
            //dd($_REQUEST);
        }

        // Optionally, you can redirect back with a success message
        return redirect()->back()->with('success', 'Service added successfully.');
    }
}
