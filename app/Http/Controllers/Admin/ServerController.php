<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\BusinessServer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ServerController extends Controller
{
    /**
     * Get all servers with advanced filtering, sorting, and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::where('role', 'server')
                ->with(['businessServers.businessLink']);

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('userid', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filter by created_by (vendor)
            if ($request->filled('vendor_id')) {
                $query->where('created_by', $request->vendor_id);
            }

            // Filter by business
            if ($request->filled('business_id')) {
                $query->whereHas('businessServers', function ($q) use ($request) {
                    $q->where('business_link_id', $request->business_id);
                });
            }

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            // Filter by email_verified
            if ($request->filled('verified')) {
                if ($request->verified === 'true' || $request->verified === '1') {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            }

            // Sorting
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['first_name', 'last_name', 'email', 'created_at', 'updated_at'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->get('per_page', 15);
            $servers = $query->paginate($perPage);

            return success('Servers fetched successfully', $servers, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Get Servers exception: ' . $exception->getMessage());
            return error('Error fetching servers', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single server details
     */
    public function show($id): JsonResponse
    {
        try {
            $server = User::where('role', 'server')
                ->with(['businessServers.businessLink', 'creator'])
                ->findOrFail($id);

            return success('Server fetched successfully', $server, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Server not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get enhanced server statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalServers = User::where('role', 'server')->count();
            $verifiedServers = User::where('role', 'server')
                ->whereNotNull('email_verified_at')
                ->count();
            $unverifiedServers = User::where('role', 'server')
                ->whereNull('email_verified_at')
                ->count();

            $assignedServers = BusinessServer::distinct('server_id')->count();
            $unassignedServers = $totalServers - $assignedServers;

            $serversByVendor = User::where('role', 'server')
                ->select('created_by', DB::raw('count(*) as count'))
                ->with('creator:id,first_name,last_name,email')
                ->groupBy('created_by')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            $recentServers = User::where('role', 'server')
                ->with(['creator:id,first_name,last_name'])
                ->latest()
                ->limit(5)
                ->get(['id', 'first_name', 'last_name', 'email', 'created_by', 'created_at']);

            return success('Server statistics fetched successfully', [
                'total_servers' => $totalServers,
                'verified_servers' => $verifiedServers,
                'unverified_servers' => $unverifiedServers,
                'assigned_servers' => $assignedServers,
                'unassigned_servers' => $unassignedServers,
                'servers_by_vendor' => $serversByVendor,
                'recent_servers' => $recentServers,
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Get Server Statistics exception: ' . $exception->getMessage());
            return error('Error fetching server statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get servers by vendor
     */
    public function getServersByVendor($vendorId): JsonResponse
    {
        try {
            $servers = User::where('role', 'server')
                ->where('created_by', $vendorId)
                ->with(['businessServers.businessLink'])
                ->get();

            return success('Vendor servers fetched successfully', $servers, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching vendor servers: ' . $e->getMessage());
            return error('Error fetching servers', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a server
     */
    public function destroy($id): JsonResponse
    {
        try {
            $server = User::where('role', 'server')->findOrFail($id);

            // Delete related business server assignments
            BusinessServer::where('server_id', $id)->delete();

            $server->delete();

            return success('Server deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Delete Server exception: ' . $exception->getMessage());
            return error('Error deleting server', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Bulk delete servers
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'server_ids' => 'required|array',
                'server_ids.*' => 'exists:users,id',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Delete business server assignments
            BusinessServer::whereIn('server_id', $request->server_ids)->delete();

            // Delete servers
            $deleted = User::where('role', 'server')
                ->whereIn('id', $request->server_ids)
                ->delete();

            return success("Successfully deleted {$deleted} servers", [
                'deleted_count' => $deleted,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting servers: ' . $e->getMessage());
            return error('Failed to delete servers', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
