<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{

    public function index(){

        $company = Company::all();
        return view('company', ['company'=>$company]);
    }

    public function addCompany(Request $request)
{
    // Validate the form data 
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
        'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
        'admin_name' => 'required|string|max:255',
        'admin_email' => 'required|email|max:255',
        'admin_phone' => 'required|string|max:20',
        'admin_address' => 'required|string|max:500',
        // Add more validation rules for other fields
    ]);

    // Insert data into the 'company' table using DB facade
    $companyID = DB::table('company')->insertGetId([
        'company_name' => $validatedData['name'],
        'company_email' => $validatedData['email'],
        'company_phone' => $validatedData['phone'],
        'company_address' => $validatedData['address'],
        'company_image' => $validatedData['upload_image'],
    ]);

    // Optionally, handle file uploading if 'upload_image' exists
    if ($request->hasFile('upload_image')) {
        $image = $request->file('upload_image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/company_images', $imageName); // Adjust storage path as needed

        // Update the 'company_image' field for the inserted record
        DB::table('company')
            ->where('company_id', $companyID)
            ->update(['company_image' => $imageName]);
    }

    // Insert data into the 'users' table using DB facade
    DB::table('users')->insert([
        'name' => $validatedData['admin_name'],
        'email' => $validatedData['admin_email'],
        'phone' => $validatedData['admin_phone'],
        'address' => $validatedData['admin_address'],
        'company_id' => $companyID,
        // Add other fields as needed
    ]);

    // Optionally, you can redirect back with a success message
    return redirect()->back()->with('success', 'Company added successfully.');
}

}
