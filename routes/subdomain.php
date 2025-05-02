<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Subdomain\VendorSiteController;

Route::get('/', [VendorSiteController::class, 'show']);
