<?php

use App\Http\Controllers\Admin\Base\AdminController;
use App\Http\Controllers\Admin\Base\AdminProfileController;
use App\Http\Controllers\Admin\Business\BusinessController;
use App\Http\Controllers\Admin\Category\CategoryController;
use App\Http\Controllers\Admin\Item\ItemController as AdminItemController;
use App\Http\Controllers\Admin\TableController as AdminTableController;
use App\Http\Controllers\Admin\ServerController as AdminServerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Vendor\ItemController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Vendor\CategoryController as VendorCategoryController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Admin\Vendor\VendorController as AdminVendorController;
use App\Http\Controllers\Admin\Subscription\AdminSubscriptionController;
use App\Http\Controllers\Admin\Payment\AdminPaymentController;
use App\Http\Controllers\Admin\Payment\AdminPaymentReconciliationController;
use App\Http\Controllers\Vendor\SubscriptionController;
use App\Http\Controllers\Vendor\PaymentController;
use App\Http\Controllers\Public\MenuController;
use App\Http\Controllers\Public\OrderController as PublicOrderController;
use App\Http\Controllers\Server\ServerController;
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
        Route::get('/items', [AdminItemController::class, 'index']);
        Route::get('/items/statistics', [AdminItemController::class, 'getStatistics']);
        Route::get('/items/{id}', [AdminItemController::class, 'showFood']);
        Route::post('/items', [AdminItemController::class, 'addFood']);
        Route::put('/items/{id}', [AdminItemController::class, 'updateFood']);
        Route::delete('/items/{id}', [AdminItemController::class, 'deleteFood']);
        Route::post('/items/bulk-update-status', [AdminItemController::class, 'bulkUpdateStatus']);
        Route::post('/items/bulk-delete', [AdminItemController::class, 'bulkDelete']);

        // Categories
        Route::get('/categories', [CategoryController::class, 'categories']);
        Route::get('/categories-with-items', [CategoryController::class, 'categoriesWithItems']);
        Route::post('/categories', [CategoryController::class, 'addCategory']);
        Route::apiResource('/categories', CategoryController::class)->except(['index', 'store']);

        // Tables
        Route::get('/tables', [AdminTableController::class, 'index']);
        Route::get('/tables/statistics', [AdminTableController::class, 'statistics']);
        Route::get('/tables/business/{businessId}', [AdminTableController::class, 'getBusinessTables']);
        Route::get('/tables/{id}', [AdminTableController::class, 'show']);
        Route::patch('/tables/{id}/status', [AdminTableController::class, 'updateStatus']);
        Route::post('/tables/bulk-update-status', [AdminTableController::class, 'bulkUpdateStatus']);
        Route::post('/tables/bulk-delete', [AdminTableController::class, 'bulkDelete']);
        Route::delete('/tables/{id}', [AdminTableController::class, 'destroy']);

        // Servers
        Route::get('/servers', [AdminServerController::class, 'index']);
        Route::get('/servers/statistics', [AdminServerController::class, 'statistics']);
        Route::get('/servers/{id}', [AdminServerController::class, 'show']);
        Route::get('/servers/vendor/{vendorId}', [AdminServerController::class, 'getServersByVendor']);
        Route::post('/servers/bulk-delete', [AdminServerController::class, 'bulkDelete']);
        Route::delete('/servers/{id}', [AdminServerController::class, 'destroy']);

        // Orders & Transactions
        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/statistics', [AdminOrderController::class, 'getStatistics']);
        Route::get('/orders/{id}', [AdminOrderController::class, 'show']);

        // QR Code Generation
        Route::post('/vendors/{id}/generate-qr', [AdminVendorController::class, 'generateQrCode']);

        // Subscription Management
        Route::group(['prefix' => 'subscriptions'], function () {
            Route::get('/', [AdminSubscriptionController::class, 'index']);
            Route::get('/plans', [AdminSubscriptionController::class, 'getPlans']);
            Route::put('/plans/{id}', [AdminSubscriptionController::class, 'updatePlan']);
            Route::get('/revenue', [AdminSubscriptionController::class, 'getRevenueStats']);
            Route::get('/{id}', [AdminSubscriptionController::class, 'show']);
            Route::patch('/{id}/status', [AdminSubscriptionController::class, 'updateStatus']);
            Route::post('/manual-upgrade', [AdminSubscriptionController::class, 'manualUpgrade']);
        });

        // Payment Management
        Route::group(['prefix' => 'payments'], function () {
            Route::get('/', [AdminPaymentController::class, 'index']);
            Route::get('/statistics', [AdminPaymentController::class, 'getStatistics']);
            Route::get('/{id}', [AdminPaymentController::class, 'show']);
            Route::patch('/{id}/complete', [AdminPaymentController::class, 'markAsCompleted']);
            Route::post('/{id}/refund', [AdminPaymentController::class, 'refund']);
        });

        // Payment Reconciliation & Disputes
        Route::group(['prefix' => 'reconciliation'], function () {
            // Reconciliation
            Route::get('/unreconciled', [AdminPaymentReconciliationController::class, 'getUnreconciledPayments']);
            Route::get('/disputed', [AdminPaymentReconciliationController::class, 'getDisputedPayments']);
            Route::get('/statistics', [AdminPaymentReconciliationController::class, 'getReconciliationStats']);
            Route::post('/payments/{paymentId}/reconcile', [AdminPaymentReconciliationController::class, 'reconcilePayment']);
            Route::post('/payments/bulk-reconcile', [AdminPaymentReconciliationController::class, 'bulkReconcile']);
            Route::get('/payments/{paymentId}/verify-paystack', [AdminPaymentReconciliationController::class, 'verifyWithPaystack']);
            Route::post('/search-discrepancies', [AdminPaymentReconciliationController::class, 'searchDiscrepancies']);

            // Disputes
            Route::get('/disputes', [AdminPaymentReconciliationController::class, 'getAllDisputes']);
            Route::get('/disputes/{disputeId}', [AdminPaymentReconciliationController::class, 'getDisputeDetails']);
            Route::post('/payments/{paymentId}/disputes', [AdminPaymentReconciliationController::class, 'reportDispute']);
            Route::patch('/disputes/{disputeId}/status', [AdminPaymentReconciliationController::class, 'updateDisputeStatus']);
        });
    });

    // Vendor Routes
    Route::group([
        'middleware' => ['authCheck', 'role:vendor'],
        'prefix' => 'vendor'
    ], function () {
        Route::get('/dashboard', [VendorController::class, 'index']);
        Route::get('/dashboard/statistics', [VendorController::class, 'getDashboardStatistics']);
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
        Route::get('/items/statistics', [ItemController::class, 'getStatistics']);
        Route::apiResource('/items', ItemController::class);
        Route::get('/items/by-category/{categoryId}', [ItemController::class, 'itemsByCategory']);

        // Media
        Route::post('/media', [VendorController::class, 'setMedia']);
        Route::post('/media/upload', [VendorController::class, 'setMedia']); // Keeping legacy route
        Route::post('/businesses/{businessId}/media', [VendorController::class, 'uploadBusinessMedia']);

        // Table Management
        Route::get('/tables', [\App\Http\Controllers\Vendor\TableController::class, 'getAllTables']);
        Route::get('/tables/statistics', [\App\Http\Controllers\Vendor\TableController::class, 'getStatistics']);
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

        // Orders & Transactions
        Route::get('/orders', [VendorOrderController::class, 'index']);
        Route::get('/orders/statistics', [VendorOrderController::class, 'getStatistics']);
        Route::get('/orders/{id}', [VendorOrderController::class, 'show']);
        Route::patch('/orders/{id}/status', [VendorOrderController::class, 'updateStatus']);
        Route::patch('/orders/{id}/payment-status', [VendorOrderController::class, 'updatePaymentStatus']);

        // Notifications
        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', [\App\Http\Controllers\Vendor\NotificationController::class, 'index']);
            Route::get('/unread', [\App\Http\Controllers\Vendor\NotificationController::class, 'unread']);
            Route::get('/unread-count', [\App\Http\Controllers\Vendor\NotificationController::class, 'unreadCount']);
            Route::post('/{id}/read', [\App\Http\Controllers\Vendor\NotificationController::class, 'markAsRead']);
            Route::post('/mark-all-read', [\App\Http\Controllers\Vendor\NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [\App\Http\Controllers\Vendor\NotificationController::class, 'destroy']);
            Route::delete('/read/all', [\App\Http\Controllers\Vendor\NotificationController::class, 'deleteAllRead']);
        });

        // Subscription Management
        Route::group(['prefix' => 'subscription'], function () {
            Route::get('/', [SubscriptionController::class, 'getCurrentSubscription']);
            Route::get('/plans', [SubscriptionController::class, 'getPlans']);
            Route::get('/history', [SubscriptionController::class, 'getSubscriptionHistory']);
            Route::post('/cancel', [SubscriptionController::class, 'cancelSubscription']);
        });

        // Payment & Upgrade
        Route::group(['prefix' => 'payment'], function () {
            Route::post('/initialize', [PaymentController::class, 'initializePayment']);
            Route::post('/verify', [PaymentController::class, 'verifyPayment']);
        });
    });

    // Paystack Webhook (public, no auth required)
    Route::post('/webhook/paystack', [PaymentController::class, 'handleWebhook']);

    // Server Routes
    Route::group([
        'middleware' => ['authCheck', 'role:server'],
        'prefix' => 'server'
    ], function () {
        // Dashboard
        Route::get('/dashboard/statistics', [ServerController::class, 'getDashboardStatistics']);
        Route::get('/profile', [ServerController::class, 'getProfile']);

        // Assigned resources
        Route::get('/assigned-tables', [ServerController::class, 'getAssignedTables']);
        Route::get('/assigned-businesses', [ServerController::class, 'getAssignedBusinesses']);

        // Orders
        Route::get('/orders', [ServerController::class, 'getOrders']);
        Route::get('/orders/statistics', [ServerController::class, 'getOrderStatistics']);
        Route::get('/orders/{id}', [ServerController::class, 'getOrder']);
        Route::patch('/orders/{id}/status', [ServerController::class, 'updateOrderStatus']);
        Route::patch('/orders/{id}/payment-status', [ServerController::class, 'updatePaymentStatus']);

        // Notifications
        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', [\App\Http\Controllers\Vendor\NotificationController::class, 'index']);
            Route::get('/unread', [\App\Http\Controllers\Vendor\NotificationController::class, 'unread']);
            Route::get('/unread-count', [\App\Http\Controllers\Vendor\NotificationController::class, 'unreadCount']);
            Route::post('/{id}/read', [\App\Http\Controllers\Vendor\NotificationController::class, 'markAsRead']);
            Route::post('/mark-all-read', [\App\Http\Controllers\Vendor\NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [\App\Http\Controllers\Vendor\NotificationController::class, 'destroy']);
        });
    });

    // Public Routes
    Route::get('/menu/{vendor_link}', [HomeController::class, 'vendor_site']);
    Route::get('/qr/{vendor_link}', [HomeController::class, 'vendor_site']); // Legacy QR route
    Route::get('/subdomain/{subdomain}', [HomeController::class, 'getVendorBySubdomain']); // Subdomain-based menu

    // Public Menu & Orders (no authentication required)
    Route::group(['prefix' => 'public'], function () {
        // Menu
        Route::get('/menu/{businessLink}', [MenuController::class, 'getMenu']);
        Route::get('/items/{itemId}', [MenuController::class, 'getItem']);
        Route::get('/tables/{tableId}', [MenuController::class, 'getTableInfo']);

        // Orders
        Route::post('/orders', [PublicOrderController::class, 'createOrder']);
        Route::post('/orders/{orderId}/payment', [PublicOrderController::class, 'processPayment']);
        Route::get('/orders/{orderNumber}', [PublicOrderController::class, 'getOrder']);
    });
});
