<?php

namespace App\Interfaces;

use App\Models\BusinessLink;
use App\Models\UserData;
use App\Models\VendorMedia;

interface VendorInterface
{
    public function getVendorDetails(string $user_id);

    public function getVendorMedia(string $user_id);

    public function getVendorById(int $id);

    public function updateVendorProfile(array $payload, int $id);

    public function verifyVendorBusinessName(string $business_name);

    public function checkVendorBusinessName(string $business_name);

    public function createVendorData(array $payload);

    public function createVendorBusinessLink(array $payload);

    public function createVendorMedia(array $payload);

}
