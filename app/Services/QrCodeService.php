<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateForVendor(string $businessLink): string
    {
        $url = config('app.url').'/menu/'.$businessLink;
        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($url);

        return CloudinaryStorage::uploadQr(
            $qrCode,
            'qr_'.$businessLink.'_'.time()
        );
    }
}
