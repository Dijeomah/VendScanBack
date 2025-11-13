<?php

use App\Http\Controllers\Admin\Base\AdminController;
use App\Http\Controllers\Admin\Base\AdminProfileController;
use App\Http\Controllers\Admin\Business\BusinessController;
use App\Http\Controllers\Admin\Category\CategoryController;
use App\Http\Controllers\Admin\TableController as AdminTableController;
use App\Http\Controllers\Admin\ServerController as AdminServerController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Vendor\ItemController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Vendor\CategoryController as VendorCategoryController;
use App\Http\Controllers\Admin\Vendor\VendorController as AdminVendorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function ($router) {
    Route::group([
        'prefix'=>'auth'
    ], function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        // Location endpoints
        Route::get('/countries', [AuthController::class, 'country']);
        Route::get('/states/{country_id}', [AuthController::class, 'state']);
        Route::get('/cities/{state_id}', [AuthController::class, 'city']);

    });

    // Admin Routes
    Route::group([
        'middleware' => ['authCheck', 'role:admin'],
        'prefix' => 'admin'
    ], function () {
        Route::get('/dashboard', [AdminController::class, 'adminDashboard']);

        // Profile Section
        Route::get('/profile', [AdminProfileController::class, 'adminProfile']);
        Route::get('/profile/edit', [AdminProfileController::class, 'editAdminProfile']);

        // Vendors
        Route::apiResource('/vendors', AdminVendorController::class);
        Route::post('/vendors/{id}/media', [AdminVendorController::class, 'setVendorMedia']);

        // Business & Links
        Route::get('/businesses', [BusinessController::class, 'getBusinesses']);
        Route::get('/business-links', [BusinessController::class, 'getBusinessLinks']);
        Route::get('/business-links/{id}', [BusinessController::class, 'getBusinessLink']);
        Route::delete('/business-links/{id}', [BusinessController::class, 'deleteBusinessLink']);

        // Items
        Route::apiResource('/items', ItemController::class)->except(['store', 'update']);
        Route::post('/items/create', [ItemController::class, 'addItem']);
        Route::put('/items/update/{id}', [ItemController::class, 'updateItem']);

        // Categories
        Route::get('/categories', [CategoryController::class, 'categories']);
        Route::get('/categories-with-items', [CategoryController::class, 'categoriesWithItems']);
        Route::post('/categories', [CategoryController::class, 'addCategory']);
        Route::apiResource('/categories', CategoryController::class)->except(['index', 'store']);

        // Tables
        Route::get('/tables', [AdminTableController::class, 'index']);
        Route::get('/tables/statistics', [AdminTableController::class, 'statistics']);
        Route::get('/tables/business/{businessId}', [AdminTableController::class, 'getBusinessTables']);
        Route::delete('/tables/{id}', [AdminTableController::class, 'destroy']);

        // Servers
        Route::get('/servers', [AdminServerController::class, 'index']);
        Route::get('/servers/statistics', [AdminServerController::class, 'statistics']);
        Route::delete('/servers/{id}', [AdminServerController::class, 'destroy']);

        // QR Code Generation
        Route::post('/vendors/{id}/generate-qr', [AdminVendorController::class, 'generateQrCode']);
    });

    // Vendor Routes
    Route::group([
        'middleware' => ['authCheck', 'role:vendor'],
        'prefix' => 'vendor'
    ], function () {
        Route::get('/dashboard', [VendorController::class, 'index']);
        Route::get('/full-profile', [VendorController::class, 'getVendorWithMenu']);

        // Profile Section
        Route::get('/profile', [VendorController::class, 'profile']);
        Route::get('/profile/edit', [VendorController::class, 'editProfile']);
        Route::put('/profile/update', [VendorController::class, 'updateProfile']);

        // Business Information
        Route::get('/business-links', [VendorController::class, 'getBusinessLinks']);
        Route::post('/business-info', [VendorController::class, 'setBusinessInfo'])->middleware('business.limit');
        Route::put('/business-info/{id}', [VendorController::class, 'updateBusinessInfo']);
        Route::post('/business-links', [VendorController::class, 'setBusinessLink'])->middleware('business.limit');
        Route::delete('/business-links/{id}', [VendorController::class, 'deleteBusinessLink']);
        Route::post('/generate-qr', [VendorController::class, 'generateQrCode']);

        // Categories
        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', [VendorCategoryController::class, 'index']);
            Route::post('/', [VendorCategoryController::class, 'store']);
            Route::post('/{categoryId}/items', [VendorCategoryController::class, 'addItemToCategory']);
        });

        // Subcategories
        Route::group(['prefix' => 'subcategories'], function () {
            Route::get('/', [VendorCategoryController::class, 'viewSubCategories']);
            Route::post('/', [VendorCategoryController::class, 'createSubCategory']);
        });

        // Items
        Route::apiResource('/items', ItemController::class);
        Route::get('/items/by-category/{categoryId}', [ItemController::class, 'itemsByCategory']);

        // Media
        Route::post('/media', [VendorController::class, 'setMedia']);
        Route::post('/media/upload', [VendorController::class, 'setMedia']); // Keeping legacy route

        // Table Management
        Route::group(['prefix' => 'businesses/{businessId}/tables'], function () {
            Route::get('/', [\App\Http\Controllers\Vendor\TableController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Vendor\TableController::class, 'store']);
            Route::post('/bulk-create', [\App\Http\Controllers\Vendor\TableController::class, 'bulkCreate']);
            Route::get('/{tableId}', [\App\Http\Controllers\Vendor\TableController::class, 'show']);
            Route::put('/{tableId}', [\App\Http\Controllers\Vendor\TableController::class, 'update']);
            Route::delete('/{tableId}', [\App\Http\Controllers\Vendor\TableController::class, 'destroy']);
        });

        // Server Management
        Route::group(['prefix' => 'servers'], function () {
            Route::get('/', [\App\Http\Controllers\Vendor\ServerController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Vendor\ServerController::class, 'store']);
            Route::get('/{serverId}', [\App\Http\Controllers\Vendor\ServerController::class, 'show']);
            Route::put('/{serverId}', [\App\Http\Controllers\Vendor\ServerController::class, 'update']);
            Route::delete('/{serverId}', [\App\Http\Controllers\Vendor\ServerController::class, 'destroy']);
            Route::post('/{serverId}/assign-business', [\App\Http\Controllers\Vendor\ServerController::class, 'assignToBusiness']);
            Route::delete('/{serverId}/remove-business', [\App\Http\Controllers\Vendor\ServerController::class, 'removeFromBusiness']);
        });

        // Server-Table Assignments
        Route::group(['prefix' => 'businesses/{businessId}'], function () {
            Route::get('/servers', [\App\Http\Controllers\Vendor\ServerController::class, 'getBusinessServers']);
            Route::get('/assignments', [\App\Http\Controllers\Vendor\TableAssignmentController::class, 'index']);
            Route::post('/assignments', [\App\Http\Controllers\Vendor\TableAssignmentController::class, 'store']);
            Route::post('/assignments/bulk', [\App\Http\Controllers\Vendor\TableAssignmentController::class, 'bulkAssign']);
            Route::delete('/assignments/{assignmentId}', [\App\Http\Controllers\Vendor\TableAssignmentController::class, 'destroy']);
            Route::patch('/assignments/{assignmentId}/status', [\App\Http\Controllers\Vendor\TableAssignmentController::class, 'updateStatus']);
            Route::get('/servers/{serverId}/assignments', [\App\Http\Controllers\Vendor\TableAssignmentController::class, 'getServerAssignments']);
            Route::get('/tables/{tableId}/assignments', [\App\Http\Controllers\Vendor\TableAssignmentController::class, 'getTableAssignments']);
        });
    });

    // Public Routes
    Route::get('/menu/{vendor_link}', [HomeController::class, 'vendor_site']);
    Route::get('/qr/{vendor_link}', [HomeController::class, 'vendor_site']); // Legacy QR route
    Route::get('/subdomain/{subdomain}', [HomeController::class, 'getVendorBySubdomain']); // Subdomain-based menu
});
