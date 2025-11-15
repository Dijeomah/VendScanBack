<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TableLinkQrData;
use App\Models\BusinessLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    /**
     * Get all tables with advanced filtering, sorting, and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = TableLinkQrData::with(['business_link.user'])
                ->select('table_link_qr_data.*');

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('table_number', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('qr_code_url', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filter by business
            if ($request->filled('business_id')) {
                $query->where('business_link_id', $request->business_id);
            }

            // Filter by vendor/user
            if ($request->filled('user_id')) {
                $query->whereHas('business_link', function ($q) use ($request) {
                    $q->where('uid', $request->user_id);
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by seats
            if ($request->filled('min_seats')) {
                $query->where('seats', '>=', $request->min_seats);
            }
            if ($request->filled('max_seats')) {
                $query->where('seats', '<=', $request->max_seats);
            }

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            // Sorting
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['table_number', 'seats', 'status', 'created_at', 'updated_at'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->get('per_page', 15);
            $tables = $query->paginate($perPage);

            return success('Tables fetched successfully', $tables, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Get Tables exception: ' . $exception->getMessage());
            return error('Error fetching tables', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get tables for a specific business
     */
    public function getBusinessTables(Request $request, $businessId): JsonResponse
    {
        try {
            $query = TableLinkQrData::where('business_link_id', $businessId);

            // Search
            if ($request->filled('search')) {
                $query->where('table_number', 'LIKE', "%{$request->search}%");
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortField = $request->get('sort_by', 'table_number');
            $sortOrder = $request->get('sort_order', 'asc');

            $allowedSortFields = ['table_number', 'seats', 'status', 'created_at'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            }

            $tables = $query->get();

            return success('Business tables fetched successfully', $tables, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Get Business Tables exception: ' . $exception->getMessage());
            return error('Error fetching business tables', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single table details
     */
    public function show($id): JsonResponse
    {
        try {
            $table = TableLinkQrData::with(['business_link.user'])
                ->findOrFail($id);

            return success('Table fetched successfully', $table, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Table not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get enhanced table statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalTables = TableLinkQrData::count();
            $activeTables = TableLinkQrData::where('status', 'active')->count();
            $occupiedTables = TableLinkQrData::where('status', 'occupied')->count();
            $reservedTables = TableLinkQrData::where('status', 'reserved')->count();
            $inactiveTables = TableLinkQrData::where('status', 'inactive')->count();

            $tablesByBusiness = BusinessLink::withCount('tables')
                ->orderBy('tables_count', 'desc')
                ->limit(10)
                ->get(['id', 'business_link', 'tables_count']);

            $totalSeats = TableLinkQrData::sum('seats');
            $averageSeats = round(TableLinkQrData::avg('seats'), 2);

            $statusDistribution = TableLinkQrData::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            $recentTables = TableLinkQrData::with(['business_link'])
                ->latest()
                ->limit(5)
                ->get(['id', 'table_number', 'business_link_id', 'status', 'seats', 'created_at']);

            return success('Table statistics fetched successfully', [
                'total_tables' => $totalTables,
                'active_tables' => $activeTables,
                'occupied_tables' => $occupiedTables,
                'reserved_tables' => $reservedTables,
                'inactive_tables' => $inactiveTables,
                'total_seats' => $totalSeats,
                'average_seats' => $averageSeats,
                'status_distribution' => $statusDistribution,
                'tables_by_business' => $tablesByBusiness,
                'recent_tables' => $recentTables,
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Get Table Statistics exception: ' . $exception->getMessage());
            return error('Error fetching table statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update table status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:active,occupied,reserved,inactive',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $table = TableLinkQrData::findOrFail($id);
            $table->status = $request->status;
            $table->save();

            return success('Table status updated successfully', $table, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating table status: ' . $e->getMessage());
            return error('Failed to update table status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Bulk update table status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'table_ids' => 'required|array',
                'table_ids.*' => 'exists:table_link_qr_data,id',
                'status' => 'required|in:active,occupied,reserved,inactive',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $updated = TableLinkQrData::whereIn('id', $request->table_ids)
                ->update(['status' => $request->status]);

            return success("Successfully updated {$updated} tables", [
                'updated_count' => $updated,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error bulk updating tables: ' . $e->getMessage());
            return error('Failed to update tables', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a table
     */
    public function destroy($id): JsonResponse
    {
        try {
            $table = TableLinkQrData::findOrFail($id);
            $table->delete();

            return success('Table deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Delete Table exception: ' . $exception->getMessage());
            return error('Error deleting table', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Bulk delete tables
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'table_ids' => 'required|array',
                'table_ids.*' => 'exists:table_link_qr_data,id',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $deleted = TableLinkQrData::whereIn('id', $request->table_ids)->delete();

            return success("Successfully deleted {$deleted} tables", [
                'deleted_count' => $deleted,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting tables: ' . $e->getMessage());
            return error('Failed to delete tables', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
