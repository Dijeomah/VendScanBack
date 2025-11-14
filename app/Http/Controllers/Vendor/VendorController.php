<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Middleware\VendorAccess;
use App\Models\BusinessLink;
use App\Models\User;
use App\Repositories\VendorRepository;
use App\Services\CloudinaryStorage;
use App\Traits\ChecksSubscriptionLimits;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VendorController extends Controller
{
    use ChecksSubscriptionLimits;
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

    /**
     * Get vendor dashboard statistics
     *
     * @return JsonResponse
     */
    public function getDashboardStatistics(): JsonResponse
    {
        try {
            $userId = auth()->id();

            // Get all businesses for this vendor
            $businesses = \App\Models\BusinessLink::with('business_data')->where('uid', $userId)->get();
            $businessIds = $businesses->pluck('id')->toArray();
            $businessLinks = $businesses->pluck('business_link')->toArray();

            // Get items for this vendor (using business_link)
            $allItems = \App\Models\Item::whereIn('business_link', $businessLinks)->get();
            $totalItems = $allItems->count();
            $activeItems = $allItems->where('status', true)->count();

            // Get categories used by this vendor
            $categoryIds = $allItems->pluck('category_id')->unique();
            $totalCategories = $categoryIds->count();

            // Get subcategories used by this vendor
            $subCategoryIds = $allItems->pluck('sub_category_id')->unique()->filter();
            $totalSubCategories = $subCategoryIds->count();

            // Get items by category for chart
            $itemsByCategory = \App\Models\Item::selectRaw('category_id, COUNT(*) as count')
                ->whereIn('business_link', $businessLinks)
                ->groupBy('category_id')
                ->with('category')
                ->get();

            // Get tables for all businesses
            $totalTables = \App\Models\TableLinkQrData::whereIn('business_link_id', $businessIds)->count();
            $activeTables = \App\Models\TableLinkQrData::whereIn('business_link_id', $businessIds)
                ->where('status', 'active')
                ->count();
            $occupiedTables = \App\Models\TableLinkQrData::whereIn('business_link_id', $businessIds)
                ->where('status', 'occupied')
                ->count();

            // Get servers for this vendor
            $totalServers = \App\Models\Server::where('id', $userId)->count();
            $activeServers = \App\Models\Server::where('id', $userId)
                ->where('status', 'active')
                ->count();

            // Get assigned servers (servers assigned to businesses)
            $assignedServers = \App\Models\BusinessServer::whereIn('business_link_id', $businessIds)
                ->where('status', 'active')
                ->distinct('server_id')
                ->count();

            // Growth data (last 7 days) - items created
            $itemGrowth = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $itemGrowth[] = [
                    'date' => $date,
                    'count' => \App\Models\Item::whereIn('business_link', $businessLinks)
                        ->whereDate('created_at', $date)
                        ->count()
                ];
            }

            // Business type distribution (if vendor has multiple businesses)
            $businessTypeDistribution = $businesses->groupBy(function($business) {
                    return $business->business_data->business_type ?? 'Other';
                })
                ->map(function ($group) {
                    return $group->count();
                })
                ->toArray();

            return success('Dashboard statistics fetched successfully', [
                'businesses' => [
                    'total' => $businesses->count(),
                    'by_type' => $businessTypeDistribution,
                    'list' => $businesses
                ],
                'items' => [
                    'total' => $totalItems,
                    'active' => $activeItems,
                    'inactive' => $totalItems - $activeItems,
                    'by_category' => $itemsByCategory
                ],
                'categories' => [
                    'total' => $totalCategories,
                    'subcategories' => $totalSubCategories
                ],
                'tables' => [
                    'total' => $totalTables,
                    'active' => $activeTables,
                    'occupied' => $occupiedTables,
                    'available' => $activeTables - $occupiedTables
                ],
                'servers' => [
                    'total' => $totalServers,
                    'active' => $activeServers,
                    'inactive' => $totalServers - $activeServers,
                    'assigned' => $assignedServers
                ],
                'growth' => [
                    'items' => $itemGrowth
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Get Vendor Dashboard Statistics exception: ' . $exception->getMessage() . ' on line: ' . $exception->getLine());
            return error('Error fetching dashboard statistics', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            $existingUserData = \App\Models\UserData::where('user_id', $userId)->first();
            $existingBusinessLink = \App\Models\BusinessLink::with('business_data')->where('uid', $userId)->first();

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
                    // Update business_data (all detailed information)
                    if ($existingBusinessLink->business_data) {
                        $existingBusinessLink->business_data->update([
                            'business_name' => $validated_data['business_name'],
                            'business_type' => $validated_data['business_type'] ?? $existingBusinessLink->business_data->business_type,
                            'phone_number' => $validated_data['phone_number'] ?? $existingBusinessLink->business_data->phone_number,
                            'address' => $validated_data['business_address'] ?? $validated_data['address'] ?? $existingBusinessLink->business_data->address,
                            'geofence_enabled' => $validated_data['geofence_enabled'] ?? $existingBusinessLink->business_data->geofence_enabled,
                            'latitude' => $validated_data['latitude'] ?? $existingBusinessLink->business_data->latitude,
                            'longitude' => $validated_data['longitude'] ?? $existingBusinessLink->business_data->longitude,
                            'geofence_radius' => $validated_data['geofence_radius'] ?? $existingBusinessLink->business_data->geofence_radius,
                        ]);
                    } else {
                        // Create business_data if it doesn't exist
                        $existingBusinessLink->business_data()->create([
                            'business_name' => $validated_data['business_name'],
                            'business_type' => $validated_data['business_type'] ?? null,
                            'phone_number' => $validated_data['phone_number'] ?? null,
                            'address' => $validated_data['business_address'] ?? $validated_data['address'] ?? null,
                            'geofence_enabled' => $validated_data['geofence_enabled'] ?? false,
                            'latitude' => $validated_data['latitude'] ?? null,
                            'longitude' => $validated_data['longitude'] ?? null,
                            'geofence_radius' => $validated_data['geofence_radius'] ?? 100,
                        ]);
                    }
                }

                return success('Business information updated successfully', [
                    'user_data' => $existingUserData,
                    'business_link' => $existingBusinessLink->fresh('business_data')
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
            // Check subscription limits for businesses
            if ($error = $this->checkLimit('businesses')) {
                return $error;
            }

            $validated_data = $this->validate($request, config('validation.set_business_name'));

            $checkBusinessLink = $this->vendorRepository->checkVendorBusinessName($validated_data['business_name']);
            if (!$checkBusinessLink || $checkBusinessLink->business_data->business_name != $validated_data['business_name']) {

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
    public function uploadBusinessMedia(int $businessId, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'header_image' => 'nullable|image|max:5120',
                'logo_image' => 'nullable|image|max:5120'
            ]);

            // Verify business belongs to vendor
            $business = BusinessLink::where('id', $businessId)
                ->where('uid', auth()->id())
                ->with('business_data')
                ->firstOrFail();

            $mediaData = [];

            if ($request->hasFile('header_image')) {
                $header = $request->file('header_image');
                $mediaData['header_image'] = CloudinaryStorage::upload(
                    $header->getRealPath(),
                    $header->getClientOriginalName()
                );
            }

            if ($request->hasFile('logo_image')) {
                $logo = $request->file('logo_image');
                $mediaData['logo_image'] = CloudinaryStorage::upload(
                    $logo->getRealPath(),
                    $logo->getClientOriginalName()
                );
            }

            if (empty($mediaData)) {
                return error('No valid files provided', null, Response::HTTP_BAD_REQUEST);
            }

            // Update or create business_data
            if ($business->business_data) {
                $business->business_data->update($mediaData);
            } else {
                $business->business_data()->create($mediaData);
            }

            $business->load('business_data');
            return success('Business media uploaded successfully', $business, Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return error('Business not found or access denied', null, Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            Log::error('Business media upload error: ' . $exception->getMessage());
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
                'geofence_enabled' => 'nullable|boolean',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'geofence_radius' => 'nullable|integer|min:1|max:10000',
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
