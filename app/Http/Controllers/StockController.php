<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
  
    public function criticalStock(): JsonResponse
    {
        $criticalProducts = Product::whereRaw('stock_quantity <= min_stock_threshold')
            ->with('department')
            ->get();

        return response()->json([
            'data' => $criticalProducts
        ]);
    }

   
    public function updateStock(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
        ]);

        $oldQuantity = $product->stock_quantity;
        $product->stock_quantity = $validated['quantity'];
        $product->save();

        
        if ($product->stock_quantity <= $product->min_stock_threshold) {
            \Log::warning("Product ID {$product->id} ({$product->name}) has low stock: {$product->stock_quantity}");
        }

        return response()->json([
            'message' => 'Stock updated successfully',
            'data' => [
                'product' => $product,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $product->stock_quantity,
            ]
        ]);
    }

    
    public function statistics(): JsonResponse
    {
       
        $outOfStock = Product::where('stock_quantity', 0)->count();

        
        $criticalStock = Product::whereRaw('stock_quantity <= min_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->count();

       
        $departmentAvgStock = DB::table('products')
            ->select('departments.name as department', DB::raw('AVG(stock_quantity) as average_stock'))
            ->join('departments', 'products.department_id', '=', 'departments.id')
            ->groupBy('departments.name')
            ->get();

        
        $departmentTotalProducts = DB::table('products')
            ->select('departments.name as department', DB::raw('COUNT(*) as total_products'))
            ->join('departments', 'products.department_id', '=', 'departments.id')
            ->groupBy('departments.name')
            ->get();

        return response()->json([
            'data' => [
                'out_of_stock_count' => $outOfStock,
                'critical_stock_count' => $criticalStock,
                'department_average_stock' => $departmentAvgStock,
                'department_product_counts' => $departmentTotalProducts,
            ]
        ]);
    }
}