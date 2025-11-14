<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Jobs\BulkCreateTablesJob;
use App\Models\TableLinkQrData;
use App\Models\BusinessLink;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TableController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Get all tables for a specific business
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

            $tables = TableLinkQrData::where('business_link_id', $businessId)
                ->with('server_assignments.server')
                ->orderBy('table_number')
                ->get();

            return success('Tables fetched successfully', $tables, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching tables: ' . $e->getMessage());
            return error('Failed to fetch tables', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new table
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
                'table_number' => 'required|string|max:50',
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

            $business = BusinessLink::where('id', $businessId)
                ->where('uid', $vendor->id)
                ->first();

            if (!$business) {
                return error('Business not found', null, Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'count' => 'required|integer|min:1|max:100',
                'prefix' => 'nullable|string|max:10',
                'seats_per_table' => 'nullable|integer|min:1|max:50'
            ]);

            $count = $validated['count'];
            $prefix = $validated['prefix'] ?? 'Table';
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
