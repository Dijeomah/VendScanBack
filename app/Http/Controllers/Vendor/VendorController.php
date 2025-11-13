<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Middleware\VendorAccess;
use App\Models\User;
use App\Repositories\VendorRepository;
use App\Services\CloudinaryStorage;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VendorController extends Controller
{
    public $vendorRepository;

    public function __construct(VendorRepository $vendorRepository)
    {
        $this->middleware('vendorCheck', ['except' => ['login', 'register']]);
        $this->middleware('auth:api');
        $this->middleware(VendorAccess::class)->except(['index', 'show']);
        $this->authorizeResource(User::class, 'vendor');
        $this->vendorRepository = $vendorRepository;
    }

    public function index(): JsonResponse
    {
        $user_id = authUser()->userid;
        $user = $this->vendorRepository->getVendorDetails($user_id);
        return success('Vendor Information ', $user, Response::HTTP_OK);
    }


    public function profile(): JsonResponse
    {
        try {
            $user_id = authUser()->userid;
            $user = $this->vendorRepository->getVendorMedia($user_id);
            return success('Vendor Information ', $user, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::debug('Profile fetch exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine());
        }
        return error('Error fetching Profile, please try again. ', [], 400);
    }

    /**
     * Edit the Authenticated User profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function editProfile(Request $request): JsonResponse
    {
        try {
            $profile = $this->vendorRepository->getVendorById(authUser()->id);
            return success('Profile information fetched', $profile, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::debug('Edit profile fetch exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine());
        }
        return error('Error getting profile, please try again. ', [], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $profile = $this->vendorRepository->updateVendorProfile($request->all(), authUser()->id);
            return success('Profile information updated', $profile, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::debug('Profile update exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine());
        }
        return error('Error updating profile, Please  try again. ', [], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setBusinessInfo(Request $request)
    {
        try {
            $validated_data = $this->validate($request, config('validation.business_info'));
            $userId = auth()->id();
            $userIdString = auth()->user()->userid;

            // Check if user already has business data
            $existingUserData = \App\Models\UserData::where('uid', $userId)->first();
            $existingBusinessLink = \App\Models\BusinessLink::where('uid', $userId)->first();

            if ($existingUserData || $existingBusinessLink) {
                // Update existing records
                if ($existingUserData) {
                    $existingUserData->update([
                        'business_name' => $validated_data['business_name'],
                        'business_address' => $validated_data['business_address'] ?? null,
                        'city_id' => $validated_data['city_id'] ?? null,
                        'state_id' => $validated_data['state_id'] ?? null,
                        'country_id' => $validated_data['country_id'] ?? null,
                    ]);
                }

                if ($existingBusinessLink) {
                    $existingBusinessLink->update([
                        'business_name' => $validated_data['business_name'],
                        'business_type' => $validated_data['business_type'] ?? null,
                        'phone_number' => $validated_data['phone_number'] ?? null,
                        'business_address' => $validated_data['business_address'] ?? null,
                        'city_id' => $validated_data['city_id'] ?? null,
                        'state_id' => $validated_data['state_id'] ?? null,
                        'country_id' => $validated_data['country_id'] ?? null,
                    ]);
                }

                return success('Business information updated successfully', [
                    'user_data' => $existingUserData,
                    'business_link' => $existingBusinessLink
                ], Response::HTTP_OK);
            } else {
                // Create new records
                $userData = $this->vendorRepository->createVendorData((array) $validated_data);
                $userBusinessData = $this->vendorRepository->createVendorBusinessLink((array) $validated_data);
                return success('Business data created successfully', [$userData, $userBusinessData], Response::HTTP_CREATED);
            }
        } catch (\Exception $exception) {
            Log::error('Set Business Info exception: ' . $exception->getMessage() . ' on line: ' . $exception->getLine());
            return error('Error saving business data: ' . $exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     */
    public function setBusinessLink(Request $request): JsonResponse
    {
        Log::debug('Set Business Link request: ' . json_encode($request->all()));
        try {
            $validated_data = $this->validate($request, config('validation.set_business_name'));

            $checkBusinessLink = $this->vendorRepository->checkVendorBusinessName($validated_data['business_name']);
            if (!$checkBusinessLink || $checkBusinessLink->business_name != $validated_data['business_name']) {

                $userData = $this->vendorRepository->createVendorBusinessLink($validated_data);
                return success('Business link created successful. ', $userData, ResponseAlias::HTTP_OK);
            }
            return error('Business link already exist, try another link. ', [], ResponseAlias::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            Log::debug('Set Business Link exception: ' . $exception->getMessage() . 'on line: ' . $exception->getLine() . 'Full Error: ' . $exception);
            return error('Error creating Business link, please try again. ', [], ResponseAlias::HTTP_BAD_REQUEST);
        }
//        return error('Error creating Business link, try again. ', [], ResponseAlias::HTTP_BAD_REQUEST);
    }

    public function setMedia(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'logo_file' => 'nullable|image',
                'hero_file' => 'nullable|image'
            ]);

            $mediaData = [];

            if ($request->hasFile('logo_file')) {
                $logo = $request->file('logo_file');
                $mediaData['logo'] = CloudinaryStorage::upload(
                    $logo->getRealPath(),
                    $logo->getClientOriginalName()
                );
            }

            if ($request->hasFile('hero_file')) {
                $hero = $request->file('hero_file');
                $mediaData['hero'] = CloudinaryStorage::upload(
                    $hero->getRealPath(),
                    $hero->getClientOriginalName()
                );
            }

            if (empty($mediaData)) {
                return error('No valid files provided', null, Response::HTTP_BAD_REQUEST);
            }

            $data = $this->vendorRepository->createVendorMedia($mediaData);
            return success('Media uploaded', $data, Response::HTTP_CREATED);

        } catch (Exception $exception) {
            Log::error('Media upload error: ' . $exception->getMessage());
            return error('Upload failed', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getVendorWithMenu()
    {
        try {
            $vendor = $this->vendorRepository->getVendorWithMenu(auth()->user()->userid);
            return success('Vendor with menu', $vendor);
        } catch (ModelNotFoundException $e) {
            return error('Vendor not found', [], 404);
        }
    }

    /**
     * Get vendor's business links with statistics
     *
     * @return JsonResponse
     */
    public function getBusinessLinks(): JsonResponse
    {
        try {
            $userId = auth()->id();
            $stats = $this->vendorRepository->getVendorBusinessStats($userId);
            return success('Business links fetched successfully', $stats, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Get Business Links exception: ' . $exception->getMessage() . ' on line: ' . $exception->getLine());
            return error('Error fetching business links', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a specific business link
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateBusinessInfo(int $id, Request $request): JsonResponse
    {
        try {
            $validated_data = $request->validate([
                'business_name' => 'sometimes|required|string|max:255',
                'business_type' => 'nullable|string|max:100',
                'phone_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
            ]);

            $business = $this->vendorRepository->updateBusinessLink($id, $validated_data);

            if (!$business) {
                return error('Business not found or unauthorized', [], Response::HTTP_NOT_FOUND);
            }

            return success('Business updated successfully', $business, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Update Business Info exception: ' . $exception->getMessage() . ' on line: ' . $exception->getLine());
            return error('Error updating business: ' . $exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a business link
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteBusinessLink(int $id): JsonResponse
    {
        try {
            $business = \App\Models\BusinessLink::where('id', $id)
                ->where('uid', auth()->id())
                ->first();

            if (!$business) {
                return error('Business not found or unauthorized', [], Response::HTTP_NOT_FOUND);
            }

            $business->delete();
            return success('Business deleted successfully', [], Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Delete Business Link exception: ' . $exception->getMessage() . ' on line: ' . $exception->getLine());
            return error('Error deleting business', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
