<?php

namespace App\Http\Controllers\Admin\Base;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserInfo(Request $request){
        $this->validate($request, config('validation.update_user_info'));

        $userid = $this->generateUserID();

        $userData = new User();
        $userData->userid = $userid;
        $userData->name = $request->name;
        $userData->role = $request->role;
        $userData->phone_number = $request->phone_number;
        $userData->email = $request->email;
        $userData->password = Hash::make($request->password);
        $userData->save();

        return response()->json('Registration Successful. ', 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminDashboard(Request $request)
    {

        $user = auth()->user();
        if ($user == $request->user()) {
            $userInfo = User::where('id', $user->id)
                ->with(['user_data.city.state'])
                ->get();
            $vendors = User::where('role', 'vendor')->get();
            return success("success", ['Admin Data: '=>$userInfo,'Vendors: '=>$vendors]);
        }
        return error("error", "No user found");
    }

}
