<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ServerTableAssignment;
use App\Models\TableLinkQrData;
use App\Models\User;
use App\Models\BusinessLink;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TableAssignmentController extends Controller
{
    /**
     * Get all table assignments for a business
     */
    public function index($businessId): JsonResponse
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

            $assignments = ServerTableAssignment::where('business_link_id', $businessId)
                ->with(['server', 'table'])
                ->get();

            return success('Table assignments fetched successfully', $assignments, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching table assignments: ' . $e->getMessage());
            return error('Failed to fetch assignments', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign a server to a table
     */
    public function store(Request $request, $businessId): JsonResponse
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

            $validated = $request->validate([
                'server_id' => 'required|exists:users,id',
                'table_id' => 'required|exists:table_link_qr_data,id'
            ]);

            // Verify server belongs to vendor
            $server = User::where('id', $validated['server_id'])
                ->where('created_by', $vendor->id)
                ->where('role', 'server')
                ->first();

            if (!$server) {
                return error('Server not found or not authorized', null, Response::HTTP_NOT_FOUND);
            }

            // Verify table belongs to business
            $table = TableLinkQrData::where('id', $validated['table_id'])
                ->where('business_link_id', $businessId)
                ->first();

            if (!$table) {
                return error('Table not found in this business', null, Response::HTTP_NOT_FOUND);
            }

            // Check if already assigned
            $exists = ServerTableAssignment::where('server_id', $validated['server_id'])
                ->where('table_id', $validated['table_id'])
                ->exists();

            if ($exists) {
                return error('Server already assigned to this table', null, Response::HTTP_CONFLICT);
            }

            $assignment = ServerTableAssignment::create([
                'server_id' => $validated['server_id'],
                'table_id' => $validated['table_id'],
                'business_link_id' => $businessId,
                'status' => 'active'
            ]);

            return success('Server assigned to table successfully', $assignment->load(['server', 'table']), Response::HTTP_CREATED);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error assigning server to table: ' . $e->getMessage());
            return error('Failed to assign server', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Bulk assign a server to multiple tables
     */
    public function bulkAssign(Request $request, $businessId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $business = BusinessLink::where('id', $businessId)
                ->where('uid', $vendor->id)
                ->first();

            if (!$business) {
                return error('Business not found', null, Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'server_id' => 'required|exists:users,id',
                'table_ids' => 'required|array|min:1',
                'table_ids.*' => 'exists:table_link_qr_data,id'
            ]);

            // Verify server belongs to vendor
            $server = User::where('id', $validated['server_id'])
                ->where('created_by', $vendor->id)
                ->where('role', 'server')
                ->first();

            if (!$server) {
                return error('Server not found or not authorized', null, Response::HTTP_NOT_FOUND);
            }

            $assignments = [];
            foreach ($validated['table_ids'] as $tableId) {
                // Verify table belongs to business
                $table = TableLinkQrData::where('id', $tableId)
                    ->where('business_link_id', $businessId)
                    ->first();

                if (!$table) continue;

                // Skip if already assigned
                $exists = ServerTableAssignment::where('server_id', $validated['server_id'])
                    ->where('table_id', $tableId)
                    ->exists();

                if ($exists) continue;

                $assignments[] = ServerTableAssignment::create([
                    'server_id' => $validated['server_id'],
                    'table_id' => $tableId,
                    'business_link_id' => $businessId,
                    'status' => 'active'
                ]);
            }

            return success('Server assigned to tables successfully', [
                'assigned_count' => count($assignments),
                'assignments' => $assignments
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error('Error bulk assigning server: ' . $e->getMessage());
            return error('Failed to assign server', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove server from a table
     */
    public function destroy($businessId, $assignmentId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $assignment = ServerTableAssignment::where('id', $assignmentId)
                ->where('business_link_id', $businessId)
                ->whereHas('business', function($query) use ($vendor) {
                    $query->where('uid', $vendor->id);
                })
                ->first();

            if (!$assignment) {
                return error('Assignment not found', null, Response::HTTP_NOT_FOUND);
            }

            $assignment->delete();

            return success('Server removed from table successfully', null, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error removing server from table: ' . $e->getMessage());
            return error('Failed to remove assignment', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get assignments for a specific server
     */
    public function getServerAssignments($businessId, $serverId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            // Verify business and server
            $business = BusinessLink::where('id', $businessId)
                ->where('uid', $vendor->id)
                ->first();

            if (!$business) {
                return error('Business not found', null, Response::HTTP_NOT_FOUND);
            }

            $assignments = ServerTableAssignment::where('business_link_id', $businessId)
                ->where('server_id', $serverId)
                ->with('table')
                ->get();

            return success('Server assignments fetched successfully', $assignments, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching server assignments: ' . $e->getMessage());
            return error('Failed to fetch assignments', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get assignments for a specific table
     */
    public function getTableAssignments($businessId, $tableId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            // Verify business and table
            $business = BusinessLink::where('id', $businessId)
                ->where('uid', $vendor->id)
                ->first();

            if (!$business) {
                return error('Business not found', null, Response::HTTP_NOT_FOUND);
            }

            $assignments = ServerTableAssignment::where('business_link_id', $businessId)
                ->where('table_id', $tableId)
                ->with('server')
                ->get();

            return success('Table assignments fetched successfully', $assignments, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching table assignments: ' . $e->getMessage());
            return error('Failed to fetch assignments', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update assignment status
     */
    public function updateStatus(Request $request, $businessId, $assignmentId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $assignment = ServerTableAssignment::where('id', $assignmentId)
                ->where('business_link_id', $businessId)
                ->whereHas('business', function($query) use ($vendor) {
                    $query->where('uid', $vendor->id);
                })
                ->first();

            if (!$assignment) {
                return error('Assignment not found', null, Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            $assignment->update(['status' => $validated['status']]);

            return success('Assignment status updated successfully', $assignment->fresh(), Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error updating assignment status: ' . $e->getMessage());
            return error('Failed to update status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
