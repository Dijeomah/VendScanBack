<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BusinessLink;
use App\Models\BusinessServer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ServerController extends Controller
{
    /**
     * Get all servers created by the vendor
     */
    public function index(): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $servers = User::where('created_by', $vendor->id)
                ->where('role', 'server')
                ->with(['assigned_businesses.business', 'assigned_tables.table'])
                ->get();

            // Add statistics for each server
            $servers->each(function ($server) {
                $today = now()->startOfDay();

                // Total orders
                $server->total_orders = \App\Models\Order::where('server_id', $server->id)->count();

                // Today's orders
                $server->today_orders = \App\Models\Order::where('server_id', $server->id)
                    ->whereDate('created_at', $today)
                    ->count();

                // Pending orders (awaiting fulfillment)
                $server->pending_orders = \App\Models\Order::where('server_id', $server->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count();

                // Fulfilled orders today
                $server->today_fulfilled = \App\Models\Order::where('server_id', $server->id)
                    ->whereDate('created_at', $today)
                    ->whereIn('status', ['completed'])
                    ->count();

                // Today's revenue
                $server->today_revenue = \App\Models\Order::where('server_id', $server->id)
                    ->whereDate('created_at', $today)
                    ->where('payment_status', 'paid')
                    ->sum('total');

                // Total revenue
                $server->total_revenue = \App\Models\Order::where('server_id', $server->id)
                    ->where('payment_status', 'paid')
                    ->sum('total');
            });

            return success('Servers fetched successfully', $servers, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching servers: ' . $e->getMessage());
            return error('Failed to fetch servers', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new server/waiter
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $validated = $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email|max:50',
                'phone_number' => 'required|unique:users,phone_number',
                'password' => 'required|min:8',
                'business_link_ids' => 'nullable|array',
                'business_link_ids.*' => 'exists:business_links,id'
            ]);

            // Generate unique userid
            $userid = 'SRV-' . strtoupper(substr(md5(uniqid()), 0, 10));

            // Create server user
            $server = User::create([
                'userid' => $userid,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'password' => Hash::make($validated['password']),
                'role' => 'server',
                'created_by' => $vendor->id
            ]);

            // Assign to businesses if specified
            if (isset($validated['business_link_ids'])) {
                foreach ($validated['business_link_ids'] as $businessId) {
                    // Verify business belongs to vendor
                    $business = BusinessLink::where('id', $businessId)
                        ->where('uid', $vendor->id)
                        ->first();

                    if ($business) {
                        BusinessServer::create([
                            'business_link_id' => $businessId,
                            'server_id' => $server->id,
                            'vendor_id' => $vendor->id,
                            'status' => 'active'
                        ]);
                    }
                }
            }

            return success('Server created successfully', $server->load('assigned_businesses'), Response::HTTP_CREATED);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error creating server: ' . $e->getMessage());
            return error('Failed to create server', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a specific server
     */
    public function show($serverId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $server = User::where('id', $serverId)
                ->where('created_by', $vendor->id)
                ->where('role', 'server')
                ->with(['assigned_businesses.business', 'assigned_tables.table'])
                ->first();

            if (!$server) {
                return error('Server not found', null, Response::HTTP_NOT_FOUND);
            }

            return success('Server fetched successfully', $server, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching server: ' . $e->getMessage());
            return error('Failed to fetch server', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a server
     */
    public function update(Request $request, $serverId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $server = User::where('id', $serverId)
                ->where('created_by', $vendor->id)
                ->where('role', 'server')
                ->first();

            if (!$server) {
                return error('Server not found', null, Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:100',
                'last_name' => 'sometimes|required|string|max:100',
                'email' => 'sometimes|required|email|max:50|unique:users,email,' . $serverId,
                'phone_number' => 'sometimes|required|unique:users,phone_number,' . $serverId,
                'password' => 'sometimes|nullable|min:8'
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $server->update($validated);

            return success('Server updated successfully', $server->fresh(), Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error updating server: ' . $e->getMessage());
            return error('Failed to update server', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a server
     */
    public function destroy($serverId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $server = User::where('id', $serverId)
                ->where('created_by', $vendor->id)
                ->where('role', 'server')
                ->first();

            if (!$server) {
                return error('Server not found', null, Response::HTTP_NOT_FOUND);
            }

            $server->delete();

            return success('Server deleted successfully', null, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error deleting server: ' . $e->getMessage());
            return error('Failed to delete server', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign server to a business
     */
    public function assignToBusiness(Request $request, $serverId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $server = User::where('id', $serverId)
                ->where('created_by', $vendor->id)
                ->where('role', 'server')
                ->first();

            if (!$server) {
                return error('Server not found', null, Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'business_link_id' => 'required|exists:business_links,id'
            ]);

            // Verify business belongs to vendor
            $business = BusinessLink::where('id', $validated['business_link_id'])
                ->where('uid', $vendor->id)
                ->first();

            if (!$business) {
                return error('Business not found', null, Response::HTTP_NOT_FOUND);
            }

            // Check if already assigned
            $exists = BusinessServer::where('business_link_id', $validated['business_link_id'])
                ->where('server_id', $serverId)
                ->exists();

            if ($exists) {
                return error('Server already assigned to this business', null, Response::HTTP_CONFLICT);
            }

            $assignment = BusinessServer::create([
                'business_link_id' => $validated['business_link_id'],
                'server_id' => $serverId,
                'vendor_id' => $vendor->id,
                'status' => 'active'
            ]);

            return success('Server assigned to business successfully', $assignment->load('business'), Response::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error('Error assigning server to business: ' . $e->getMessage());
            return error('Failed to assign server', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove server from a business
     */
    public function removeFromBusiness(Request $request, $serverId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $validated = $request->validate([
                'business_link_id' => 'required|exists:business_links,id'
            ]);

            $assignment = BusinessServer::where('business_link_id', $validated['business_link_id'])
                ->where('server_id', $serverId)
                ->where('vendor_id', $vendor->id)
                ->first();

            if (!$assignment) {
                return error('Assignment not found', null, Response::HTTP_NOT_FOUND);
            }

            $assignment->delete();

            return success('Server removed from business successfully', null, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error removing server from business: ' . $e->getMessage());
            return error('Failed to remove server', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get servers for a specific business
     */
    public function getBusinessServers($businessId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            // Verify business belongs to vendor
            $business = BusinessLink::where('id', $businessId)
                ->where('uid', $vendor->id)
                ->first();

            if (!$business) {
                return error('Business not found', null, Response::HTTP_NOT_FOUND);
            }

            $servers = BusinessServer::where('business_link_id', $businessId)
                ->with('server')
                ->get();

            return success('Business servers fetched successfully', $servers, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching business servers: ' . $e->getMessage());
            return error('Failed to fetch servers', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
