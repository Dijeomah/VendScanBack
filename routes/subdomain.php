<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubdomainController;

Route::get('/', [SubdomainController::class, 'handle']);
