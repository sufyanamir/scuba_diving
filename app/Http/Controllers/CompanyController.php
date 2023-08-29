<?php

// app/Http/Controllers/CompanyController.php

use App\Http\Controllers\Controller;
use App\Models\Company; // Assuming your Company model is named like this
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function addCompany(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            // Add more validation rules for other fields
        ]);

        // Create a new Company instance and fill it with validated data
        $company = new Company();
        $company->company_name = $validatedData['name'];
        $company->company_email = $validatedData['email'];
        $company->company_phone = $validatedData['phone'];
        $company->company_address = $validatedData['address'];
        if ($request->hasFile('upload_image')) {
            // Get the uploaded file
            $image = $request->file('upload_image');
            
            // Generate a unique name for the image
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            
            // Store the image in the specified storage location
            $image->storeAs('public/company_images', $imageName); // Adjust storage path as needed
        
            // Save the image file name to the database
            $company->upload_image = $imageName;
        }
        // Set other fields here
        
        // Save the company data
        $company->save();

        // Optionally, you can redirect back with a success message
        return redirect()->back()->with('success', 'Company added successfully.');
    }
}

