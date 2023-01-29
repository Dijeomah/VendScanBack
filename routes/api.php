<?php

    use App\Http\Controllers\Admin\Base\AdminController;
    use App\Http\Controllers\Admin\Base\AdminProfileController;
    use App\Http\Controllers\Admin\Business\BusinessController;
    use App\Http\Controllers\Admin\Category\CategoryController;
    use App\Http\Controllers\Auth\AuthController;
    use App\Http\Controllers\HomeController;
    use App\Http\Controllers\Vendor\FoodController;
    use App\Http\Controllers\Vendor\VendorController;
    use \App\Http\Controllers\Admin\Vendor\VendorController as AdminVendorController;
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

    Route::group(['middleware' => 'api',], function ($router) {
        Route::group([
            'prefix' => 'auth'
        ], function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });
//    creating the Admin routes
        /*
         * Dashboard
         * Vendors - CRUD
         *
         * */
        Route::group([
            'middleware' => 'authCheck',
            'prefix' => 'admin'
        ], function () {
            Route::get('/dashboard', [AdminController::class, 'adminDashboard']);

//          Profile Section
            Route::get('/profile', [AdminProfileController::class, 'adminProfile']);
            Route::get('/profile/edit', [AdminProfileController::class, 'editAdminProfile']);

//            Vendors
            Route::apiResource('/vendor',AdminVendorController::class);

//            Business & Links
            Route::get('/business', [BusinessController::class, 'getBusinesses']);
            Route::get('/business/links', [BusinessController::class, 'getBusinessLinks']);

//            Food
            Route::get('/food',[FoodController::class, 'food']);
            Route::post('/food/create',[FoodController::class,'addFood']);
            Route::get('/food/{id}', [FoodController::class,'showFood']);
            Route::get('/food/edit/{id}', [FoodController::class,'editFood']);
            Route::put('/food/update/{id}', [FoodController::class,'updateFood']);
            Route::delete('/food/delete/{id}', [FoodController::class,'deleteFood']);

//          Category Section
            Route::get('/categories', [CategoryController::class, 'categories']);
            Route::post('/category/create', [CategoryController::class, 'addCategory']);
            Route::get('/category/show/{id}', [CategoryController::class, 'showCategory']);
            Route::get('/category/edit/{id}', [CategoryController::class, 'editCategory']);
            Route::put('/category/update', [CategoryController::class, 'updateCategory']);
            Route::delete('/category/delete/{id}', [CategoryController::class, 'deleteCategory']);

        });

        Route::group([
            'middleware' => 'vendorCheck',
            'prefix' => 'vendor'
        ], function () {
            Route::get('/dashboard', [VendorController::class, 'index']);
            Route::get('/profile/edit', [VendorController::class, 'editProfile']);
            Route::put('/profile/update', [VendorController::class, 'updateProfile']);

            Route::post('/create/business-info', [VendorController::class, 'setBusinessInfo']);
            Route::post('/create/business-link', [VendorController::class, 'setBusinessLink']);

            //
            Route::get('/food', [FoodController::class, 'food']);
            Route::post('/food/create', [FoodController::class, 'addFood']);
            Route::get('/food/show/{id}', [FoodController::class, 'showFood']);
            Route::get('/food/edit/{id}', [FoodController::class, 'editFood']);
            Route::put('/food/update/{id}', [FoodController::class, 'updateFood']);
            Route::delete('/food/delete/{id}', [FoodController::class, 'deleteFood']);

        });
        Route::get('/qr/{vendor_link}', [HomeController::class, 'vendor_site']);

    });
