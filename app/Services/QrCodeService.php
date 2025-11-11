<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\CloudinaryStorage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        try {
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->generate($url);

            // Check if Cloudinary is configured
            if (config('cloudinary.cloud_url')) {
                try {
                    // Try to upload to Cloudinary if configured
                    $base64 = base64_encode($qrCode);
                    $dataUri = 'data:image/svg+xml;base64,' . $base64;

                    $cloudinaryUrl = CloudinaryStorage::uploadQr(
                        $dataUri,
                        'qr_' . $businessLink . '_' . time()
                    );

                    Log::info('QR code uploaded to Cloudinary successfully');
                    return $cloudinaryUrl;
                } catch (\Exception $cloudinaryError) {
                    // If Cloudinary upload fails, log the error and fall back to local storage
                    Log::error('Cloudinary upload failed, falling back to local storage: ' . $cloudinaryError->getMessage());

                    // Fall through to local storage
                }
            }

            // Use local storage (either Cloudinary not configured or upload failed)
            Log::warning('Storing QR code locally');

            $filename = 'qr_codes/qr_' . $businessLink . '_' . time() . '.svg';
            Storage::disk('public')->put($filename, $qrCode);

            return Storage::disk('public')->url($filename);

        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage(). '::On File::'.$e->getFile().'::On Line::'.$e->getLine());
            throw $e;
        }
    }
}
