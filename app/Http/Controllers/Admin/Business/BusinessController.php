<?php

namespace App\Http\Controllers\Admin\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessLink;
use App\Models\UserData;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    //
    public function getBusinesses(){
        try {
            $business = UserData::latest()->paginate(10);
            return success('Businesses fetched', $business, 200);
        }catch (\Exception $exception)
        {
            return failed('No record found.', [], 404);
        }
    }

    public function getBusinessLinks(){
        try {
            $businessLink = BusinessLink::latest()->paginate(10);
            return success('Business links fetched', $businessLink, 200);
        }catch (\Exception $exception)
        {
            return failed('No record found.', [], 404);
        }
    }

    public function getBusinessLink($id)
    {
        try {
            $businessLink = BusinessLink::findOrFail($id);
            return success('Business link fetched', $businessLink, 200);
        }catch (\Exception $exception)
        {
            return failed('No record found.', [], 404);
        }
    }

    public function deleteBusinessLink($id)
    {
        try {
            $businessLink = BusinessLink::find($id);
            $businessLink->delete();
            return success('Business link deleted', [], 200);
        }catch (\Exception $exception)
        {
            return failed('Error in deleting business link.', [], 404);
        }
    }

}
