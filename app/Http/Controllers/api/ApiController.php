<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        try {
            $email = $request->input('email');
            $token = Str::random(60);
            $password = $request->input('password');
            $user = User::where('email', $email)->first();

            if (!$user || md5($password) !== $user->password) {
                // Authentication failed
                return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
            }

            // Check if the user role is 0 or 1
            $userRole = $user->user_role;
            if ($userRole != 0 && $userRole != 1 && $userRole != 2) {
                // User role is not allowed to login
                return response()->json(['success' => false, 'message' => 'This user has no access to login'], 401);
            }

            // Create a session for the user
            session(['user_details' => [
                'token' => $token, // Set token value if needed
                'name' => $user->name,
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'role' => $userRole,
            ]]);

            return response()->json(['success' => true, 'message' => 'Login successful', 'user_details' => session('user_details')], 200);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            return response()->json(['success' => false, 'message' => 'An error occurred while processing your request', 'error' => $e->getMessage()], 500);
        }
    }
}
