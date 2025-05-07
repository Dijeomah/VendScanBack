<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\CloudinaryStorage;

class MediaService
{
    public function handleVendorMedia(array $files, int $vendorId): array
    {
        $results = [];

        foreach (['logo_file', 'hero_file'] as $type) {
            if (isset($files[$type])) {
                $file = $files[$type];
                $results[$type] = CloudinaryStorage::upload(
                    $file->getRealPath(),
                    "{$type}_{$vendorId}_".time()
                );
            }
        }
        return $results;
    }
}
