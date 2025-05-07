<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\CloudinaryStorage;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    public function generateForVendor(string $businessLink): string
    {
        $url = config('app.url') . '/reach/' . $businessLink;
        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($url);
        $base64 = base64_encode($qrCode);
        $dataUri = 'data:image/png;base64,' . $base64;

        return CloudinaryStorage::uploadQr(
            $dataUri,
            'qr_' . $businessLink . '_' . time()
        );
    }
}
