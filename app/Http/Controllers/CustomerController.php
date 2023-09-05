<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CustomerController extends Controller
{

    public function index(){
        
        // dd($customers);
        $user_details = session('user_details');

        $userId = $user_details['user_id'];

        $customers = Customers::where('added_user_id', $userId)->get();

        return view('customers', ['customers'=>$customers, 'userDetails'=>$user_details]);
    }
    

    public function addCustomer(Request $request){

        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'company_id' => 'required',
            'added_user_id' => 'required',
            // Add more validation rules for other fields
        ]);
        $fbAcc = $request->input('fb_acc');
        $igAcc = $request->input('ig_acc');
        $ttAcc = $request->input('tt_acc');

        $status = 1;

        $socailLinks="$fbAcc,$igAcc,$ttAcc";

        DB::table('customers')->insert([
            'customer_name' => $validatedData['name'],
            'customer_email' => $validatedData['email'],
            'customer_phone' => $validatedData['phone'],
            'customer_address' => $validatedData['address'],
            'customer_image' => $validatedData['upload_image'],
            'company_id' => $validatedData['company_id'],
            'added_user_id' => $validatedData['added_user_id'],
            'customer_social_links' => $socailLinks,
            'customer_status' => $status,
            // Add other fields as needed
        ]);

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
        return redirect()->back()->with('success', 'Customer added successfully.');

    }

    public function destroy($id)
    {
        $user = Customers::find($id);
        $path = 'storage/customer_images/'.$user->customer_image;
        if (File::exists($path)) {
            
            File::delete($path);
        }
        $user->delete();
        return redirect('customers')->with('status','Customer Deleted successfully');


    }
}
