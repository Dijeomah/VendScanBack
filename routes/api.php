<?php

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

    Route::group(['middleware' => 'api',], function ($router) {
        Route::group([
            'prefix' => 'auth'
        ], function ($auth_router) {
            Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register']);
            Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
            Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
            Route::post('/refresh', [\App\Http\Controllers\Auth\AuthController::class, 'refresh']);
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
        ], function ($auth_router) {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\Base\AdminController::class, 'adminDashboard']);
//          Profile Section
            Route::get('/profile', [\App\Http\Controllers\Admin\Base\AdminProfileController::class, 'adminProfile']);
            Route::get('/profile/edit', [\App\Http\Controllers\Admin\Base\AdminProfileController::class, 'editAdminProfile']);


            Route::apiResource('/vendor',\App\Http\Controllers\Admin\Vendor\VendorController::class);

//            Business & Links
            Route::get('/business', [\App\Http\Controllers\Admin\Business\BusinessController::class, 'getBusinesses']);
            Route::get('/business/links', [\App\Http\Controllers\Admin\Business\BusinessController::class, 'getBusinessLinks']);

//            Food
            Route::get('/food',[\App\Http\Controllers\Admin\Food\FoodController::class, 'food']);
            Route::post('/food/create',[\App\Http\Controllers\Admin\Food\FoodController::class,'addFood']);
            Route::get('/food/{id}', [\App\Http\Controllers\Admin\Food\FoodController::class,'showFood']);
            Route::get('/food/edit/{id}', [\App\Http\Controllers\Admin\Food\FoodController::class,'editFood']);
            Route::put('/food/update/{id}', [\App\Http\Controllers\Admin\Food\FoodController::class,'updateFood']);
            Route::delete('/food/delete/{id}', [\App\Http\Controllers\Admin\Food\FoodController::class,'deleteFood']);



//          Category Section
            Route::get('/categories', [\App\Http\Controllers\Admin\Category\CategoryController::class, 'categories']);
            Route::post('/category/create', [\App\Http\Controllers\Admin\Category\CategoryController::class, 'addCategory']);
            Route::get('/category/show/{id}', [\App\Http\Controllers\Admin\Category\CategoryController::class, 'showCategory']);
            Route::get('/category/edit/{id}', [\App\Http\Controllers\Admin\Category\CategoryController::class, 'editCategory']);
            Route::put('/category/update', [\App\Http\Controllers\Admin\Category\CategoryController::class, 'updateCategory']);
            Route::delete('/category/delete/{id}', [\App\Http\Controllers\Admin\Category\CategoryController::class, 'deleteCategory']);


        });

        Route::group([
            'middleware' => 'vendorCheck',
            'prefix' => 'vendor'
        ], function ($auth_router) {
            Route::get('/dashboard', [\App\Http\Controllers\Vendor\VendorController::class, 'index']);
            Route::get('/profile/edit', [\App\Http\Controllers\Vendor\VendorController::class, 'editProfile']);
            Route::get('/profile/update', [\App\Http\Controllers\Vendor\VendorController::class, 'updateProfile']);

            Route::post('/business-info', [\App\Http\Controllers\Vendor\VendorController::class, 'setBusinessInfo']);
            Route::post('/business-link', [\App\Http\Controllers\Vendor\VendorController::class, 'setBusinessLink']);

            //
            Route::get('/food', [\App\Http\Controllers\Vendor\FoodController::class, 'food']);
            Route::post('/food/create', [\App\Http\Controllers\Vendor\FoodController::class, 'addFood']);
            Route::get('/food/show/{id}', [\App\Http\Controllers\Vendor\FoodController::class, 'showFood']);
            Route::get('/food/edit/{id}', [\App\Http\Controllers\Vendor\FoodController::class, 'editFood']);
            Route::put('/food/update/{id}', [\App\Http\Controllers\Vendor\FoodController::class, 'updateFood']);
            Route::delete('/food/delete/{id}', [\App\Http\Controllers\Vendor\FoodController::class, 'deleteFood']);

        });
        Route::get('/qr/{vendor_link}', [\App\Http\Controllers\HomeController::class, 'vendor_site']);

    });
