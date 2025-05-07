<?php

namespace App\Http\Controllers\Admin\Base;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function updateUserInfo(Request $request){
    // Validate request data
    $validatedData = $this->validate($request, config('validation.update_user_info'));

    // Start a database transaction
    DB::beginTransaction();

    try {
        // Generate a unique user ID
        $userid = $this->generateUserID();

        // Create a new user object
        $userData = new User();
        $userData->userid = $userid;
        $userData->name = $validatedData['name'];
        $userData->role = $validatedData['role'];
        $userData->phone_number = $validatedData['phone_number'];
        $userData->email = $validatedData['email'];
        $userData->password = Hash::make($validatedData['password']);
        $userData->save();

        // Commit the transaction
        DB::commit();
    } catch (Exception $e) {
        // Rollback the transaction on failure
        DB::rollBack();
        throw $e; // Re-throw the exception to handle it elsewhere
    }

    // Return success response
    return success('User Info Updated Successfully. ', 200);
}


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminDashboard(Request $request)
{
    $user = auth()->user();

    // Check if the user is authenticated
    if (!$user) {
        return error("error", "User not authenticated");
    }

    // Verify if the authenticated user matches the requested user
    if ($user->id !== $request->user()->id) {
        return error("error", "No user found");
    }

    // Fetch the user info with optimized eager loading
    $userInfo = User::where('id', $user->id)
        ->with([
            'user_data.city.state'
        ])
        ->first();

    // Fetch vendor data
    $vendors = User::where('role', 'vendor')->get();

    // Return success response
    return success("success", ['admin_data' => $userInfo, 'vendor_data' => $vendors]);
}


}
