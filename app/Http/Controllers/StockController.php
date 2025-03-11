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
        $validated = $request->validated();
    
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

    /**
 * @OA\Get(
 *     path="/v1/stock/statistics",
 *     summary="Get stock statistics",
 *     tags={"Stock"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Stock statistics",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="out_of_stock_count", type="integer", example=5),
 *                 @OA\Property(property="critical_stock_count", type="integer", example=12),
 *                 @OA\Property(
 *                     property="department_average_stock",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="department", type="string", example="Electronics"),
 *                         @OA\Property(property="average_stock", type="number", format="float", example=45.5)
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="department_product_counts",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="department", type="string", example="Electronics"),
 *                         @OA\Property(property="total_products", type="integer", example=25)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */
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