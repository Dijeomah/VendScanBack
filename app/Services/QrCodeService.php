<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\CloudinaryStorage;
use Illuminate\Support\Facades\Log;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    public function generateForVendor(string $businessLink): string
    {
        $url = config('app.url') . '/reach/' . $businessLink;

        // Use SVG format to avoid needing imagick or GD for generation
        // Then convert to PNG only if needed
        try {
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->generate($url);

            // For base64 encoding, we'll use SVG directly
            // This avoids the need for image libraries
            $base64 = base64_encode($qrCode);
            $dataUri = 'data:image/svg+xml;base64,' . $base64;

            return CloudinaryStorage::uploadQr(
                $dataUri,
                'qr_' . $businessLink . '_' . time()
            );
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
