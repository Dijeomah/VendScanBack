<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BusinessLink;
use App\Models\Category;
use App\Models\Item;
use App\Models\TableLinkQrData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuController extends Controller
{
    /**
     * Get menu for a business
     */
    public function getMenu(string $businessLink): JsonResponse
    {
        try {
            $business = BusinessLink::where('business_link', $businessLink)->first();

            if (!$business) {
                return error('Business not found', null, Response::HTTP_NOT_FOUND);
            }

            // Get all items for this business with categories
            $items = Item::where('business_link', $businessLink)
                ->where('status', true)
                ->with(['category', 'sub_category'])
                ->orderBy('category_id')
                ->orderBy('price')
                ->get();

            // Group items by category
            $categories = Category::whereIn('id', $items->pluck('category_id')->unique())
                ->with(['subcategories' => function ($query) use ($businessLink) {
                    $query->whereHas('items', function ($q) use ($businessLink) {
                        $q->where('business_link', $businessLink)
                          ->where('status', true);
                    })->withCount(['items' => function ($q) use ($businessLink) {
                        $q->where('business_link', $businessLink)
                          ->where('status', true);
                    }]);
                }])
                ->get();

            return success('Menu fetched successfully', [
                'business' => $business,
                'categories' => $categories,
                'items' => $items->groupBy('category_id'),
                'total_items' => $items->count(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Error fetching menu: ' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single item details
     */
    public function getItem(int $itemId): JsonResponse
    {
        try {
            $item = Item::with(['category', 'sub_category', 'business'])
                ->where('id', $itemId)
                ->where('status', true)
                ->first();

            if (!$item) {
                return error('Item not found', null, Response::HTTP_NOT_FOUND);
            }

            return success('Item fetched successfully', $item, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Error fetching item', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get table information
     */
    public function getTableInfo(int $tableId): JsonResponse
    {
        try {
            $table = TableLinkQrData::with('businessLink')->find($tableId);

            if (!$table) {
                return error('Table not found', null, Response::HTTP_NOT_FOUND);
            }

            return success('Table info fetched successfully', $table, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Error fetching table info', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
