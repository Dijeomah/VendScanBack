<?php

namespace App\Repositories;

use App\Interfaces\VendorInterface;
use App\Models\BusinessLink;
use App\Models\User;
use App\Models\UserData;
use App\Models\VendorMedia;
use App\Services\QrCodeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VendorRepository implements VendorInterface
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function getVendorDetails(string $userId)
    {
        return User::with([
            'user_data.business_links.items.category',
            'vendor_media'
        ])
            ->where('userid', $userId)
            ->firstOrFail();
    }

    public function getVendorMedia(string $userId)
    {
        return User::with(['vendor_media'])
            ->where('userid', $userId)
            ->firstOrFail()
            ->vendor_media;
    }

    public function getVendorById(int $id)
    {
        return User::with(['user_data', 'vendor_media'])
            ->findOrFail($id);
    }

    public function updateVendorProfile(array $payload, int $id): bool
    {
        try {
            return User::where('id', $id)->update([
                'first_name' => $payload['first_name'],
                'last_name' => $payload['last_name'],
                'phone_number' => $payload['phone_number'],
                'email' => $payload['email'],
            ]);
        } catch (\Exception $e) {
            Log::error("Failed updating vendor profile: {$e->getMessage()}");
            return false;
        }
    }

    public function verifyVendorBusinessName(string $businessName): bool
    {
        return UserData::where('business_name', $businessName)->exists();
    }

    public function checkVendorBusinessName(string $businessName): ?BusinessLink
    {
        return BusinessLink::whereHas('business_data', function ($query) use ($businessName) {
            $query->where('business_name', $businessName);
        })->with('business_data')->first();
    }

    public function createVendorData(array $payload): UserData
    {
        return UserData::create([
            'uid' => auth()->id(),
            'userid' => auth()->user()->userid,
            'business_name' => $payload['business_name'],
            'business_address' => $payload['business_address'],
            'city_id' => $payload['city_id'],
            'state_id' => $payload['state_id'],
            'country_id' => $payload['country_id'],
        ]);
    }

    public function createVendorBusinessLink(array $payload): BusinessLink
    {
        $slug = Str::slug($payload['business_name'] ?? $payload['business_link']);
        $count = BusinessLink::where('business_link', $slug)->count();
        $uniqueSlug = $count > 0 ? "{$slug}-{$count}" : $slug;

        // Create the business link (core identifiers only)
        $businessLink = BusinessLink::create([
            'uid' => auth()->id(),
            'userid' => auth()->user()->userid,
            'business_link' => $uniqueSlug,
            'subdomain' => $uniqueSlug,
            'business_qr' => $this->qrCodeService->generateForVendor($uniqueSlug),
        ]);

        // Create the business data (all detailed information)
        $businessLink->business_data()->create([
            'business_name' => $payload['business_name'] ?? $uniqueSlug,
            'business_type' => $payload['business_type'] ?? null,
            'phone_number' => $payload['phone_number'] ?? null,
            'address' => $payload['address'] ?? $payload['business_address'] ?? null,
            'latitude' => $payload['latitude'] ?? null,
            'longitude' => $payload['longitude'] ?? null,
            'geofence_radius' => $payload['geofence_radius'] ?? 100,
            'geofence_enabled' => $payload['geofence_enabled'] ?? false,
        ]);

        return $businessLink->fresh(['business_data']);
    }

    public function createVendorMedia(array $payload): VendorMedia
    {
        return VendorMedia::updateOrCreate(
            ['vendor_id' => auth()->id()],
            $payload
        );
    }

    public function getVendorWithMenu(string $userId)
    {
        $fullVendor =  User::with([
            'business_links.items' => function ($query) {
                $query->where('status', true)
                    ->orderBy('category_id')
                    ->orderBy('price');
            },
            'business_links.items.category',
            'business_links.business_data',
            'categories' => function ($query) {
                $query->orderBy('category_name');
            },
            'vendor_media'
        ])->where('userid', $userId)->first();

        if (!$fullVendor) {
            return [];
        }
        return $fullVendor;
    }

    public function getVendorBusinessLinks(int $userId)
    {
        return BusinessLink::with(['items', 'business_data'])
            ->where('uid', $userId)
            ->latest()
            ->get();
    }

    public function getVendorBusinessStats(int $userId): array
    {
        $businesses = $this->getVendorBusinessLinks($userId);
        $totalItems = 0;

        foreach ($businesses as $business) {
            $totalItems += $business->items->count();
        }

        return [
            'total_businesses' => $businesses->count(),
            'total_items' => $totalItems,
            'businesses' => $businesses
        ];
    }

    public function updateBusinessLink(int $businessId, array $payload): ?BusinessLink
    {
        $business = BusinessLink::with('business_data')
            ->where('id', $businessId)
            ->where('uid', auth()->id())
            ->first();

        if (!$business) {
            return null;
        }

        // Update business_data (all detailed information)
        if ($business->business_data) {
            $business->business_data->update([
                'business_name' => $payload['business_name'] ?? $business->business_data->business_name,
                'business_type' => $payload['business_type'] ?? $business->business_data->business_type,
                'phone_number' => $payload['phone_number'] ?? $business->business_data->phone_number,
                'address' => $payload['address'] ?? $business->business_data->address,
                'geofence_enabled' => $payload['geofence_enabled'] ?? $business->business_data->geofence_enabled,
                'latitude' => $payload['latitude'] ?? $business->business_data->latitude,
                'longitude' => $payload['longitude'] ?? $business->business_data->longitude,
                'geofence_radius' => $payload['geofence_radius'] ?? $business->business_data->geofence_radius,
            ]);
        } else {
            // Create business_data if it doesn't exist
            $business->business_data()->create([
                'business_name' => $payload['business_name'] ?? $business->business_link,
                'business_type' => $payload['business_type'] ?? null,
                'phone_number' => $payload['phone_number'] ?? null,
                'address' => $payload['address'] ?? null,
                'geofence_enabled' => $payload['geofence_enabled'] ?? false,
                'latitude' => $payload['latitude'] ?? null,
                'longitude' => $payload['longitude'] ?? null,
                'geofence_radius' => $payload['geofence_radius'] ?? 100,
            ]);
        }

        return $business->fresh(['items', 'business_data']);
    }
}
