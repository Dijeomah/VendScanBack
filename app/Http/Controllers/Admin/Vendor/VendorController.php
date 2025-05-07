<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorMedia;
use App\Services\CloudinaryStorage;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $vendors = User::where('role', 'vendor')->paginate(20);
        return success('Vendors', $vendors, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, config('validation.registration'));
        $userid = $this->generateUserID();

        try {
            $userData = new User();
            $userData->userid = $userid;
            $userData->first_name = $request->first_name;
            $userData->last_name = $request->last_name;
            $userData->role = 'vendor';
            $userData->phone_number = $request->phone_number;
            $userData->email = $request->email;
            $userData->password = Hash::make($request->password);
            $userData->save();
            return success('Vendor Created Successful. ', $userData, 201);

        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
            return error($exception->getMessage()) ;
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        //
        $profile = User::where('role', 'vendor')->with('business_links')->find($id);
        if ($profile) {
            return success('Vendor Profile information fetched', $profile, Response::HTTP_OK);
        }
        return error('Vendor not found', [], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
        $category = User::find($id);
        $category->category_name = $request->category_name;
        $category->save();
        return success('Category information updated', $category, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $vendor = User::find($id);
        $vendor->delete();
        return success('Vendor information updated', $vendor, 200);
    }

    public function setVendorMedia(Request $request, MediaService $mediaService): JsonResponse
    {
        $validated = $request->validate([
            'logo' => 'nullable|image|max:2048',
            'hero' => 'nullable|image|max:5120'
        ]);

        try {
            $media = $mediaService->handleVendorMedia($validated, $request->user()->id);

            $vendorMedia = VendorMedia::updateOrCreate(
                ['vendor_id' => $request->user()->id],
                $media
            );

            return success('Media uploaded', $vendorMedia, 201);
        } catch (\Exception $e) {
            Log::error('Media upload failed: '.$e->getMessage());
            return error('Media upload failed', [], 500);
        }
    }
}
