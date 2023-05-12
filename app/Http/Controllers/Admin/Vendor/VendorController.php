<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Http\Controllers\CloudinaryStorage;
use App\Http\Controllers\Controller;
use App\Models\BusinessLink;
use App\Models\User;
use App\Models\VendorMedia;
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

        $userData = new User();
        $userData->userid = $userid;
        $userData->first_name = $request->first_name;
        $userData->last_name = $request->last_name;
        $userData->role = $request->role;
        $userData->phone_number = $request->phone_number;
        $userData->email = $request->email;
        $userData->password = Hash::make($request->password);
        $userData->save();
        return success('Vendor Created Successful. ',$userData, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        //
        $profile = User::find($id);
        return success('Vendor Profile information fetched', $profile, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
        $category= User::find($id);
        $category->category_name = $request->category_name;
        $category->save();
        return success('Category information updated', $category, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $vendor= User::find($id);
        $vendor->delete();
        return success('Vendor information updated', $vendor, 200);
    }

    public function setVendorMedia(Request $request, $id): JsonResponse
    {
        $validated_data = $this->validate($request, config('validation.admin_set_vendor_media'));
        $logo_file = $request->file($validated_data['logo_file']);
        $hero_file = $request->file($validated_data['hero_file']);
        try {

            if (!empty([$logo_file || $hero_file])) {
                switch ($logo_file || $hero_file) {
                    case isset($logo_file) && isset($hero_file):
                        $logo_data = CloudinaryStorage::upload($logo_file->getRealPath(), $logo_file->getClientOriginalName());
                        $hero_data = CloudinaryStorage::upload($hero_file->getRealPath(), $hero_file->getClientOriginalName());

                        // Store file reference in database

                        $data = VendorMedia::updateOrCreate(
                            ['vendor_id' => $id],
                            ['logo' => $logo_data, 'hero' => $hero_data]
                        );
                        return success('Business logo and hero created successful. ', $data, Response::HTTP_CREATED);
                        break;

                    case isset($logo_file):
                        $logo_data = CloudinaryStorage::upload($logo_file->getRealPath(), $logo_file->getClientOriginalName());

                        Log::debug($logo_data);

                        // Store file reference in database
                        $data = VendorMedia::updateOrCreate(
                            ['vendor_id' => $id],
                            ['logo' => $logo_data]
                        );
                        return success('Business logo created successful. ', $data, Response::HTTP_CREATED);
                        break;

                    case isset($hero_file):
                        $hero_data = CloudinaryStorage::upload($hero_file->getRealPath(), $hero_file->getClientOriginalName());

                        // Store file reference in database
                        $data = VendorMedia::updateOrCreate(
                            ['vendor_id' => $id],
                            ['hero' => $hero_data]
                        );
                        return success('Business hero created successful. ', $data, Response::HTTP_CREATED);
                        break;

                    default:
                        return error('Something went wrong while uploading. ', [], Response::HTTP_BAD_REQUEST);
                }
            }

        } catch (\Exception $exception) {
            Log::debug('Set Business Media exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine());
        }
        return error('Files are empty or not supported. ', [], Response::HTTP_BAD_REQUEST);
    }
}
