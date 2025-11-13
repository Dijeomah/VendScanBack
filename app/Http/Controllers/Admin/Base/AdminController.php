<?php

namespace App\Http\Controllers\Admin\Base;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BusinessLink;
use App\Models\Item;
use App\Models\Category;
use App\Models\TableLinkQrData;
use App\Models\Server;
use App\Models\BusinessServer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function updateUserInfo(Request $request){
    // Validate request data
    $validatedData = $this->validate($request, config('validation.update_user_info'));

    // Start a database transaction
    DB::beginTransaction();

    try {
        // Generate a unique user ID
        $userid = $this->generateUserID();

        // Create a new user object
        $userData = new User();
        $userData->userid = $userid;
        $userData->name = $validatedData['name'];
        $userData->role = $validatedData['role'];
        $userData->phone_number = $validatedData['phone_number'];
        $userData->email = $validatedData['email'];
        $userData->password = Hash::make($validatedData['password']);
        $userData->save();

        // Commit the transaction
        DB::commit();
    } catch (Exception $e) {
        // Rollback the transaction on failure
        DB::rollBack();
        throw $e; // Re-throw the exception to handle it elsewhere
    }

    // Return success response
    return success('User Info Updated Successfully. ', 200);
}


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminDashboard(Request $request)
{
    $user = auth()->user();

    // Check if the user is authenticated
    if (!$user) {
        return error("error", "User not authenticated");
    }

    // Verify if the authenticated user matches the requested user
    if ($user->id !== $request->user()->id) {
        return error("error", "No user found");
    }

    // Fetch the user info with optimized eager loading
    $userInfo = User::where('id', $user->id)
        ->with([
            'user_data.city.state'
        ])
        ->first();

    // Comprehensive statistics for dashboard
    $statistics = $this->getDashboardStatistics();

    // Return success response
    return success("success", [
        'admin_data' => $userInfo,
        'statistics' => $statistics
    ]);
}

    /**
     * Get comprehensive dashboard statistics
     *
     * @return array
     */
    private function getDashboardStatistics()
    {
        // Vendor statistics
        $totalVendors = User::where('role', 'vendor')->count();
        $activeVendors = User::where('role', 'vendor')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Business statistics
        $totalBusinesses = BusinessLink::count();
        $businessesByVendor = BusinessLink::select('uid', DB::raw('count(*) as count'))
            ->groupBy('uid')
            ->get();

        // Item statistics
        $totalItems = Item::count();
        $activeItems = Item::where('status', true)->count();
        $itemsByCategory = Item::select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Category statistics
        $totalCategories = Category::count();

        // Table statistics
        $totalTables = TableLinkQrData::count();
        $activeTables = TableLinkQrData::where('status', 'active')->count();
        $occupiedTables = TableLinkQrData::where('status', 'occupied')->count();

        // Server statistics
        $totalServers = Server::count();
        $activeServers = Server::where('status', 'active')->count();
        $assignedServers = BusinessServer::where('status', 'active')
            ->distinct('server_id')
            ->count();

        // Growth data (last 7 days)
        $vendorGrowth = [];
        $businessGrowth = [];
        $itemGrowth = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $vendorGrowth[] = [
                'date' => $date,
                'count' => User::where('role', 'vendor')
                    ->whereDate('created_at', $date)
                    ->count()
            ];
            $businessGrowth[] = [
                'date' => $date,
                'count' => BusinessLink::whereDate('created_at', $date)->count()
            ];
            $itemGrowth[] = [
                'date' => $date,
                'count' => Item::whereDate('created_at', $date)->count()
            ];
        }

        // Recent vendors
        $recentVendors = User::where('role', 'vendor')
            ->with(['business_links'])
            ->latest()
            ->limit(5)
            ->get();

        // Business type distribution
        $businessTypeDistribution = BusinessLink::select('business_type', DB::raw('count(*) as count'))
            ->whereNotNull('business_type')
            ->groupBy('business_type')
            ->get();

        return [
            'vendors' => [
                'total' => $totalVendors,
                'active_last_30_days' => $activeVendors,
                'recent' => $recentVendors
            ],
            'businesses' => [
                'total' => $totalBusinesses,
                'by_vendor' => $businessesByVendor,
                'by_type' => $businessTypeDistribution
            ],
            'items' => [
                'total' => $totalItems,
                'active' => $activeItems,
                'inactive' => $totalItems - $activeItems,
                'by_category' => $itemsByCategory
            ],
            'categories' => [
                'total' => $totalCategories
            ],
            'tables' => [
                'total' => $totalTables,
                'active' => $activeTables,
                'occupied' => $occupiedTables,
                'available' => $activeTables - $occupiedTables
            ],
            'servers' => [
                'total' => $totalServers,
                'active' => $activeServers,
                'inactive' => $totalServers - $activeServers,
                'assigned' => $assignedServers
            ],
            'growth' => [
                'vendors' => $vendorGrowth,
                'businesses' => $businessGrowth,
                'items' => $itemGrowth
            ]
        ];
    }


}
