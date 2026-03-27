<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\CentralLogics\CategoryLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get_categories(Request $request)
    {
        $vendor = $request['vendor'];
        try {
            $categories = Category::where(['position' => 0, 'status' => 1])
                ->when(config('module.current_module_data'), function ($query) {
                    $query->module(config('module.current_module_data')['id']);
                })
                ->orderBy('priority', 'desc')
                ->select('categories.*')
                ->selectRaw('categories.*, ( SELECT COUNT(*)
                            FROM items WHERE items.store_id = ?
                            AND JSON_CONTAINS(items.category_ids, JSON_OBJECT("id", CAST(categories.id AS CHAR)), "$") AND JSON_CONTAINS(items.category_ids, JSON_OBJECT("position", 1), "$") ) AS products_count', [$vendor->stores[0]->id])
                ->get();



            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([$e], 200);
        }
    }

    public function get_childes(Request $request, $id)
    {
        $vendor = $request['vendor'];

        try {
            $categories = Category::query();
            if (is_numeric($id)) {
                $categories = $categories->where('parent_id', $id);
            } else {
                $categories = $categories->whereHas('parent', function ($query) use ($id) {
                    $query->where('slug', $id);
                });
            }
            $categories = $categories->where('status', 1)->orderBy('priority', 'desc')
                ->withCount(['products' => function ($query) use ($vendor) {
                    $query->where('store_id', $vendor->stores[0]->id);
                }])
                ->get();

            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function getCategoryWiseProducts(Request $request, $id)
    {
        $vendor = $request['vendor'];
        $limit = $request['limit'] ?? 25;
        $offset = $request['offset'] ?? 1;
        $items = Item::where('store_id', $vendor->stores[0]->id)
        ->where('is_approved', 1);
        if($request->sub_category == 1){
            $items = $items->where('category_id', $id);
        } else {
            $items = $items->whereRaw(
                "JSON_CONTAINS(category_ids, JSON_OBJECT('id', CAST(? AS CHAR), 'position', ?), '$')",
                [$id, 1]
            );
        }

        $items = $items->paginate($limit, ['*'], 'page', $offset);

        $formated_items = Helpers::product_data_formatting($items->items(), true, false, app()->getLocale());

        $data = [
            'total_size' => $items->total(),
            'limit' => $limit,
            'offset' => $offset,
            'items' => $formated_items
        ];

        return response()->json($data, 200);
    }
}
