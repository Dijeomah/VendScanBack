<?php

    use App\Http\Controllers\Admin\Base\AdminController;
    use App\Http\Controllers\Admin\Base\AdminProfileController;
    use App\Http\Controllers\Admin\Business\BusinessController;
    use App\Http\Controllers\Admin\Category\CategoryController;
    use App\Http\Controllers\Auth\AuthController;
    use App\Http\Controllers\HomeController;
    use App\Http\Controllers\Vendor\ItemController;
    use App\Http\Controllers\Vendor\VendorController;
    use App\Http\Controllers\Vendor\CategoryController as VendorCategoryController;
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
            Route::apiResource('/vendor', AdminVendorController::class);

//            Business & Links
            Route::get('/business', [BusinessController::class, 'getBusinesses']);
            Route::get('/business/links', [BusinessController::class, 'getBusinessLinks']);

//            Item
            Route::get('/item', [ItemController::class, 'item']);
            Route::post('/item/create', [ItemController::class, 'addItem']);
            Route::get('/item/{id}', [ItemController::class, 'showItem']);
            Route::get('/item/edit/{id}', [ItemController::class, 'editItem']);
            Route::put('/item/update/{id}', [ItemController::class, 'updateItem']);
            Route::delete('/item/delete/{id}', [ItemController::class, 'deleteItem']);

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
            Route::get('/profile', [VendorController::class, 'profile']);
            Route::get('/profile/edit', [VendorController::class, 'editProfile']);
            Route::put('/profile/update', [VendorController::class, 'updateProfile']);

            // Business Information Section
            Route::post('/create/business-info', [VendorController::class, 'setBusinessInfo']);
            Route::post('/create/business-link', [VendorController::class, 'setBusinessLink']);

            // Category Section
            Route::group([
                'prefix'=>'categories'
            ], function (){
                Route::get('/',[VendorCategoryController::class, 'index']);
                Route::post('/create',[VendorCategoryController::class, 'store']);

            });

            Route::group([
                'prefix'=>'sub-categories'
            ], function (){
               Route::post('/create', [VendorCategoryController::class, 'createSubCategory']);
            });

            //Item Section
            Route::group([
                'prefix'=>'item'
            ], function (){
                Route::get('/', [ItemController::class, 'item']);
                Route::post('/create', [ItemController::class, 'addItem']);
                Route::get('/show/{id}', [ItemController::class, 'showItem']);
                Route::get('/edit/{id}', [ItemController::class, 'editItem']);
                Route::put('/update/{id}', [ItemController::class, 'updateItem']);
                Route::delete('/delete/{id}', [ItemController::class, 'deleteItem']);

            });

            //Business Media Section
            Route::post('/media/upload', [VendorController::class, 'setMedia']);

        });

    });
    Route::get('/qr/{vendor_link}', [HomeController::class, 'vendor_site']);
