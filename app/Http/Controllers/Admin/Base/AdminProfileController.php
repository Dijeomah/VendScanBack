<?php

namespace App\Http\Controllers\Admin\Base;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminProfileController extends Controller
{
    //
    public function adminProfile(Request $request){
        $adminData = $request->user();
        return success('Profile information fetched.',$adminData, 200);
    }

    public function editAdminProfile(Request $request){
        $profile = User::find($request->user()->id);
        return success('Profile information for fetched', $profile, 200);
    }


}
