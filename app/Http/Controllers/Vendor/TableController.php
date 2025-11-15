<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Jobs\BulkCreateTablesJob;
use App\Models\TableLinkQrData;
use App\Models\BusinessLink;
use App\Services\QrCodeService;
use App\Traits\ChecksSubscriptionLimits;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TableController extends Controller
{
    use ChecksSubscriptionLimits;

    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Get all tables for a specific business with filtering and sorting
     */
    public function index(Request $request, $businessId): JsonResponse
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

            $query = TableLinkQrData::where('business_link_id', $businessId)
                ->with('server_assignments.server');

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('table_number', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('table_name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('notes', 'LIKE', "%{$searchTerm}%");
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

            // Filter by assigned server
            if ($request->filled('has_server')) {
                if ($request->has_server === 'true' || $request->has_server === '1') {
                    $query->has('server_assignments');
                } else {
                    $query->doesntHave('server_assignments');
                }
            }

            // Sorting
            $sortField = $request->get('sort_by', 'table_number');
            $sortOrder = $request->get('sort_order', 'asc');

            $allowedSortFields = ['table_number', 'table_name', 'seats', 'status', 'created_at', 'updated_at'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            } else {
                $query->orderBy('table_number', 'asc');
            }

            // Check if pagination is requested
            if ($request->filled('paginate') && $request->paginate === 'true') {
                $perPage = $request->get('per_page', 15);
                $tables = $query->paginate($perPage);
            } else {
                $tables = $query->get();
            }

            return success('Tables fetched successfully', $tables, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching tables: ' . $e->getMessage());
            return error('Failed to fetch tables', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all tables across all vendor businesses with filtering
     */
    public function getAllTables(Request $request): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $query = TableLinkQrData::whereHas('business_link', function ($q) use ($vendor) {
                $q->where('uid', $vendor->id);
            })->with(['business_link', 'server_assignments.server']);

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('table_number', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('table_name', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filter by business
            if ($request->filled('business_id')) {
                $query->where('business_link_id', $request->business_id);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['table_number', 'seats', 'status', 'created_at'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            }

            $perPage = $request->get('per_page', 15);
            $tables = $query->paginate($perPage);

            return success('Tables fetched successfully', $tables, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching all tables: ' . $e->getMessage());
            return error('Failed to fetch tables', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get table statistics for vendor
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $vendor = Auth::user();

            // Get business ID if provided, otherwise aggregate all businesses
            $businessId = $request->get('business_id');

            $query = TableLinkQrData::query();

            if ($businessId) {
                // Verify business belongs to vendor
                $business = BusinessLink::where('id', $businessId)
                    ->where('uid', $vendor->id)
                    ->first();

                if (!$business) {
                    return error('Business not found', null, Response::HTTP_NOT_FOUND);
                }

                $query->where('business_link_id', $businessId);
            } else {
                // All tables for all vendor's businesses
                $query->whereHas('business_link', function ($q) use ($vendor) {
                    $q->where('uid', $vendor->id);
                });
            }

            $stats = [
                'total_tables' => $query->count(),
                'active_tables' => (clone $query)->where('status', 'active')->count(),
                'occupied_tables' => (clone $query)->where('status', 'occupied')->count(),
                'reserved_tables' => (clone $query)->where('status', 'reserved')->count(),
                'inactive_tables' => (clone $query)->where('status', 'inactive')->count(),
                'total_seats' => $query->sum('seats'),
                'average_seats' => round($query->avg('seats'), 2),
            ];

            return success('Table statistics fetched successfully', $stats, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching table statistics: ' . $e->getMessage());
            return error('Failed to fetch statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new table
     */
    public function store(Request $request, $businessId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            // Check subscription limits for tables
            if ($error = $this->checkLimit('tables')) {
                return $error;
            }

            // Verify business belongs to vendor
            $business = BusinessLink::where('id', $businessId)
                ->where('uid', $vendor->id)
                ->first();

            if (!$business) {
                return error('Business not found', null, Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'table_number' => 'required|integer|min:1|max:50',
                'table_name' => 'nullable|string|max:100',
                'seats' => 'nullable|integer|min:1|max:50',
                'notes' => 'nullable|string|max:500'
            ]);

            // Check if table number already exists for this business
            $exists = TableLinkQrData::where('business_link_id', $businessId)
                ->where('table_number', $validated['table_number'])
                ->exists();

            if ($exists) {
                return error('Table number already exists for this business', null, Response::HTTP_CONFLICT);
            }

            // Generate QR code URL with table context
            $qrCodeUrl = $business->subdomain . '.localhost:3000?table=' . $validated['table_number'];
            $qrCode = $this->qrCodeService->generateTableQR($business->subdomain, $validated['table_number']);

            $table = TableLinkQrData::create([
                'business_link_id' => $businessId,
                'table_number' => $validated['table_number'],
                'table_name' => $validated['table_name'] ?? null,
                'seats' => $validated['seats'] ?? 4,
                'notes' => $validated['notes'] ?? null,
                'qr_code_url' => $qrCodeUrl,
                'table_qr_code' => $qrCode,
                'status' => 'active'
            ]);

            return success('Table created successfully', $table, Response::HTTP_CREATED);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error creating table: ' . $e->getMessage());
            return error('Failed to create table', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a specific table
     */
    public function show($businessId, $tableId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $table = TableLinkQrData::where('id', $tableId)
                ->where('business_link_id', $businessId)
                ->whereHas('business_link', function($query) use ($vendor) {
                    $query->where('uid', $vendor->id);
                })
                ->with(['server_assignments.server', 'business_link'])
                ->first();

            if (!$table) {
                return error('Table not found', null, Response::HTTP_NOT_FOUND);
            }

            return success('Table fetched successfully', $table, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching table: ' . $e->getMessage());
            return error('Failed to fetch table', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a table
     */
    public function update(Request $request, $businessId, $tableId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $table = TableLinkQrData::where('id', $tableId)
                ->where('business_link_id', $businessId)
                ->whereHas('business_link', function($query) use ($vendor) {
                    $query->where('uid', $vendor->id);
                })
                ->first();

            if (!$table) {
                return error('Table not found', null, Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'table_number' => 'sometimes|required|string|max:50',
                'table_name' => 'nullable|string|max:100',
                'seats' => 'nullable|integer|min:1|max:50',
                'status' => 'sometimes|in:active,inactive,occupied,reserved',
                'notes' => 'nullable|string|max:500'
            ]);

            // Check if new table number already exists (excluding current table)
            if (isset($validated['table_number'])) {
                $exists = TableLinkQrData::where('business_link_id', $businessId)
                    ->where('table_number', $validated['table_number'])
                    ->where('id', '!=', $tableId)
                    ->exists();

                if ($exists) {
                    return error('Table number already exists for this business', null, Response::HTTP_CONFLICT);
                }

                // Regenerate QR code if table number changed
                $business = $table->business_link;
                $qrCodeUrl = $business->subdomain . '.localhost:3000?table=' . $validated['table_number'];
                $qrCode = $this->qrCodeService->generateTableQR($business->subdomain, $validated['table_number']);

                $validated['qr_code_url'] = $qrCodeUrl;
                $validated['table_qr_code'] = $qrCode;
            }

            $table->update($validated);

            return success('Table updated successfully', $table->fresh(), Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error updating table: ' . $e->getMessage());
            return error('Failed to update table', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a table
     */
    public function destroy($businessId, $tableId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            $table = TableLinkQrData::where('id', $tableId)
                ->where('business_link_id', $businessId)
                ->whereHas('business_link', function($query) use ($vendor) {
                    $query->where('uid', $vendor->id);
                })
                ->first();

            if (!$table) {
                return error('Table not found', null, Response::HTTP_NOT_FOUND);
            }

            $table->delete();

            return success('Table deleted successfully', null, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error deleting table: ' . $e->getMessage());
            return error('Failed to delete table', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Bulk create tables (dispatches job for async processing)
     */
    public function bulkCreate(Request $request, $businessId): JsonResponse
    {
        try {
            $vendor = Auth::user();

            // Check subscription limits for tables (bulk creation)
            // We need to check if creating this many tables would exceed the limit
            $user = Auth::user();
            $plan = $user->subscriptionPlan ?? \App\Models\SubscriptionPlan::where('slug', 'free')->first();

            $validated = $request->validate([
                'count' => 'required|integer|min:1|max:100',
//                'prefix' => 'nullable|string|max:10',
                'seats_per_table' => 'nullable|integer|min:1|max:50'
            ]);

            $count = $validated['count'];

            // Check if user can create this many tables
            $currentCount = $user->getResourceCount('tables');
            $limit = $plan->getLimit('tables');

            if ($limit !== null && ($currentCount + $count) > $limit) {
                $remaining = max(0, $limit - $currentCount);
                return error(
                    "Cannot create {$count} tables. You have {$remaining} table slots remaining in your {$plan->name} plan. Upgrade to create more.",
                    [
                        'resource' => 'tables',
                        'current_count' => $currentCount,
                        'requested_count' => $count,
                        'limit' => $limit,
                        'remaining' => $remaining,
                        'limit_reached' => true,
                        'upgrade_required' => true,
                        'available_plans' => $this->getUpgradePlans($plan),
                    ],
                    403
                );
            }

            $business = BusinessLink::where('id', $businessId)
                ->where('uid', $vendor->id)
                ->first();

            if (!$business) {
                return error('Business not found', null, Response::HTTP_NOT_FOUND);
            }

//            $prefix = $validated['prefix'] ?? 'Table';
            $prefix = null;
            $seats = $validated['seats_per_table'] ?? 4;

            // Dispatch the job to handle table creation asynchronously
            BulkCreateTablesJob::dispatch($businessId, $count, $prefix, $seats, $vendor->id);

            Log::info("Bulk table creation job dispatched for business {$businessId}, creating {$count} tables");

            return success('Bulk table creation started. Tables are being created in the background. Please refresh the page in a few moments.', [
                'message' => 'Tables are being created in the background',
                'count' => $count,
                'estimated_time' => ceil($count / 2) . ' seconds'
            ], Response::HTTP_ACCEPTED);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error dispatching bulk table creation: ' . $e->getMessage());
            return error('Failed to start table creation', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
