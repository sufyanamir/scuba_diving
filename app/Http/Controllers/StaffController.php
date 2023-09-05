<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StaffController extends Controller
{

    public function index(){
        // Retrieve the user's id from the session
        $userDetails = session('user_details');
        $companyId = $userDetails['company_id'];
    
        // Retrieve staff members that have the same added_user_id as the user's id and user_role '2'
        $staff = User::where('user_role', '2')
                     ->where('company_id', $companyId)
                     ->get();
    
        return view('staff', compact('staff', 'userDetails'));
    }
    
    

    public function addStaff(Request $request){

        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'category' => 'required|string|max:500',
            'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'company_id' => 'required',
            // Add more validation rules for other fields
        ]);
        $fbAcc = $request->input('fb_acc');
        $igAcc = $request->input('ig_acc');
        $ttAcc = $request->input('tt_acc');

        $socailLinks="$fbAcc,$igAcc,$ttAcc";

        DB::table('users')->insert([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'category' => $validatedData['category'],
            'user_image' => $validatedData['upload_image'],
            'company_id' => $validatedData['company_id'],
            'social_links' => $socailLinks,
            'user_role'=>'2'
            // Add other fields as needed
        ]);

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
        return redirect()->back()->with('success', 'Staff added successfully.');

    }
    public function destroy($id)
    {
        $user = User::find($id);
        $path = 'storage/staff_images/'.$user->user_image;
        if (File::exists($path)) {
            
            File::delete($path);
        }
        $user->delete();
        return redirect('staff')->with('status','Staff Deleted successfully');


    }
}
