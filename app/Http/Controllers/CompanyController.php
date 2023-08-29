<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{

    public function showCompany()
    {
        // Use a custom select query to retrieve specific columns
        $companies = DB::table('company')
            ->select('company_id', 'company_name', 'company_email', 'company_phone', 'company_address')
            ->get();

        return view('company', compact('companies'));
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

        // dd($validatedData);
        // Create a new Company instance and fill it with validated data
        // Insert data into the database using the DB facade
        DB::table('company')->insert([
            'company_name' => $validatedData['name'],
            'company_email' => $validatedData['email'],
            'company_phone' => $validatedData['phone'],
            'company_address' => $validatedData['address'],
            'company_image' => $validatedData['upload_image'],
            // Add other fields as needed
        ]);
        $companyID = DB::getPdo()->lastInsertId();

        DB::table('users')->insert([
            'name' => $validatedData['admin_name'],
            'email' => $validatedData['admin_email'],
            'phone' => $validatedData['admin_phone'],
            'address' => $validatedData['admin_address '],
            'company_id' => $companyID,
            // Add other fields as needed
        ]);

        // Optionally, you can check if the 'upload_image' field exists and handle file uploading
        if ($request->hasFile('upload_image')) {
            // Get the uploaded file
            $image = $request->file('upload_image');

            // Generate a unique name for the image
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Store the image in the specified storage location
            $image->storeAs('public/company_images', $imageName); // Adjust storage path as needed

            // Now, if you want to associate the uploaded image filename with the inserted record, you would need to retrieve the last inserted ID.
            $lastInsertedId = DB::getPdo()->lastInsertId();

            // Update the 'upload_image' field for the inserted record
            DB::table('company')
                ->where('company_id', $lastInsertedId)
                ->update(['company_image' => $imageName]);
        }

        // Optionally, you can redirect back with a success message
        return redirect()->back()->with('success', 'Company added successfully.');
    }
}
