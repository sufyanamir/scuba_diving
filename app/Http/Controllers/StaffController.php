<?php

namespace App\Http\Controllers;

use App\Mail\StaffRegistrationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{

    public function index()
    {
        // Retrieve the user's id from the session
        $userDetails = session('user_details');
        $companyId = $userDetails['company_id'];

        // Retrieve staff members that have the same added_user_id as the user's id and user_role '2'
        $staff = User::where('user_role', '2')
            ->where('company_id', $companyId)
            ->get();

        return view('staff', compact('staff', 'userDetails'));
    }



    public function addStaff(Request $request)
    {


        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|regex:/^[0-9]+$/|max:20',
            'address' => 'required|string|max:500',
            'category' => 'required|string|max:500',
            'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'company_id' => 'required',
            // Add more validation rules for other fields
        ]);
        $fbAcc = $request->input('fb_acc');
        $igAcc = $request->input('ig_acc');
        $ttAcc = $request->input('tt_acc');

        $socailLinks = "$fbAcc,$igAcc,$ttAcc";
        $password = rand();

        $dataToInsert = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'category' => $validatedData['category'],
            'company_id' => $validatedData['company_id'],
            'social_links' => $socailLinks,
            'user_role' => '2',
            'app_url' => 'https://scubadiving.thewebconcept.tech/',
            'password' => md5($password),
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
        }else {
            $lastInsertedId = DB::getPdo()->lastInsertId();

            // Update the 'upload_image' field for the inserted record
            DB::table('users')
                ->where('id', $lastInsertedId)
                ->update(['user_image' => 'assets/images/user.png']);
        }
        $emailData = [
            'email' => $validatedData['email'],
            'password' => $password,
        ];
        $mail = new StaffRegistrationMail($emailData);

        try {
            Mail::to($validatedData['email'])->send($mail);
        } catch (\Exception $e) {
            return redirect('/staff')->with('status', 'Something went wrong with the email');
        }

        // Optionally, you can redirect back with a success message
        return redirect('/staff')->with('status', 'Staff added successfully.');

    }

    public function update(Request $request, $id)
    {
        $staff = User::find($id);
    
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|regex:/^[0-9]+$/|max:20',
            'address' => 'required|string|max:500',
            'category' => 'required|string|max:500',
            'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'company_id' => 'required',
            // Add more validation rules for other fields
        ]);
    
        if ($request->hasFile('upload_image')) {
            // Correctly build the old image path
            $oldImage = str_replace('storage/staff_images/', '', $staff->user_image);
            $path = 'public/staff_images/' . $oldImage;
    
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
    
            $image = $request->file('upload_image');
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . "." . $ext;
            $image->storeAs('public/staff_images', $imageName);
            $staff->user_image = 'storage/staff_images/' . $imageName; // Correctly assign the new image path
        }
    
        // Other data updates...
        $fbAcc = $request->input('fb_acc');
        $igAcc = $request->input('ig_acc');
        $ttAcc = $request->input('tt_acc');
        $socailLinks = "$fbAcc,$igAcc,$ttAcc";
    
        $staff->name = $validatedData['name'];
        $staff->email = $validatedData['email'];
        $staff->phone = $validatedData['phone'];
        $staff->address = $validatedData['address'];
        $staff->category = $validatedData['category'];
        $staff->company_id = $validatedData['company_id'];
        $staff->social_links = $socailLinks;
        $staff->update();
    
        return redirect('staff')->with('status', 'Staff Updated Successfully');
    }
    



    // public function destroy($id)
    // {
    //     $user = User::find($id);
    //     $path = 'storage/staff_images/' . $user->user_image;
    //     if (File::exists($path)) {

    //         File::delete($path);
    //     }
    //     $user->delete();
    //     return redirect('/staff')->with('status', 'Staff Deleted successfully');
    // }
    public function destroy($id)
{
    $user = User::find($id);

    // Correctly build the path for the storage
    $oldImage = str_replace('storage/staff_images/', '', $user->user_image);
    $path = 'public/staff_images/' . $oldImage;

    // Check if the image file exists in the storage and delete it
    if (Storage::exists($path)) {
        Storage::delete($path);
    }

    // Delete the user record
    $user->delete();

    return redirect('/staff')->with('status', 'Staff Deleted successfully');
}

}
