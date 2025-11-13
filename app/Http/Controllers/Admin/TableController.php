<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TableLinkQrData;
use App\Models\BusinessLink;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TableController extends Controller
{
    /**
     * Get all tables across all businesses
     */
    public function index()
    {
        try {
            $tables = TableLinkQrData::with(['business_link'])
                ->latest()
                ->paginate(50);

            return success('Tables fetched successfully', $tables, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Get Tables exception: ' . $exception->getMessage());
            return error('Error fetching tables', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get tables for a specific business
     */
    public function getBusinessTables($businessId)
    {
        try {
            $tables = TableLinkQrData::where('business_link_id', $businessId)
                ->latest()
                ->get();

            return success('Business tables fetched successfully', $tables, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Get Business Tables exception: ' . $exception->getMessage());
            return error('Error fetching business tables', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get table statistics
     */
    public function statistics()
    {
        try {
            $totalTables = TableLinkQrData::count();
            $activeTables = TableLinkQrData::where('status', 'active')->count();
            $occupiedTables = TableLinkQrData::where('status', 'occupied')->count();
            $tablesByBusiness = BusinessLink::withCount('tables')->get();

            return success('Table statistics fetched successfully', [
                'total_tables' => $totalTables,
                'active_tables' => $activeTables,
                'occupied_tables' => $occupiedTables,
                'tables_by_business' => $tablesByBusiness
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Get Table Statistics exception: ' . $exception->getMessage());
            return error('Error fetching table statistics', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a table
     */
    public function destroy($id)
    {
        try {
            $table = TableLinkQrData::findOrFail($id);
            $table->delete();

            return success('Table deleted successfully', [], Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Delete Table exception: ' . $exception->getMessage());
            return error('Error deleting table', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
