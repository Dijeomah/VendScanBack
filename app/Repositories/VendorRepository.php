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

    public function getVendorDetails(mixed $userId)
    {
        return User::with([
            'user_data.business_links.items.category',
            'vendor_media'
        ])
            ->where('userid', $userId)
            ->firstOrFail();
    }

    public function getVendorMedia(mixed $userId)
    {
        return User::with(['vendor_media'])
            ->where('userid', $userId)
            ->firstOrFail()
            ->vendor_media;
    }

    public function getVendorById(mixed $id)
    {
        return User::with(['user_data', 'vendor_media'])
            ->findOrFail($id);
    }

    public function updateVendorProfile(array $payload, mixed $id): bool
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

    public function verifyVendorBusinessName(mixed $businessName): bool
    {
        return UserData::where('business_name', $businessName)->exists();
    }

    public function checkVendorBusinessName(mixed $businessName): ?BusinessLink
    {
        return BusinessLink::where('business_name', $businessName)->first();
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
        $slug = Str::slug($payload['business_name']);
        $count = BusinessLink::where('business_link', $slug)->count();
        $uniqueSlug = $count > 0 ? "{$slug}-{$count}" : $slug;

        return BusinessLink::create([
            'uid' => auth()->id(),
            'userid' => auth()->user()->userid,
            'business_name' => $payload['business_name'],
            'business_link' => $uniqueSlug,
            'business_qr' => $this->qrCodeService->generateForVendor($uniqueSlug),
        ]);
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
        return User::with([
            'businessLinks.items' => function($query) {
                $query->where('status', true)
                    ->orderBy('category_id')
                    ->orderBy('price');
            },
            'businessLinks.items.category',
            'vendor_media'
        ])
            ->where('userid', $userId)
            ->firstOrFail();
    }
}
