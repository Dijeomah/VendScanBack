<?php

namespace App\Interfaces;

use App\Models\BusinessLink;
use App\Models\UserData;
use App\Models\VendorMedia;

interface VendorInterface
{
    public function getVendorDetails($user_id);

    public function getVendorMedia($user_id);

    public function getVendorById($id);

    public function updateVendorProfile(array $payload, $id);

    public function verifyVendorBusinessName($business_name);

    public function checkVendorBusinessName($business_name);

    public function createVendorData(array $payload);

    public function createVendorBusinessLink(array $payload);

    public function createVendorMedia(array $payload);

}
