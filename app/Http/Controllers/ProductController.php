<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        $query = Product::query();

        
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        
        if ($request->has('promotional') && $request->promotional) {
            $query->where('is_promotional', true);
        }

        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();

        return response()->json([
            'data' => $products
        ]);
    }

    /**
     * Store a newly created product in storage.
     * 
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_threshold' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'is_promotional' => 'nullable|boolean',
            'department_id' => 'required|exists:departments,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $product = Product::create($validated);

        
        $this->checkStockThreshold($product);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }


    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'data' => $product
        ]);
    }

    /**
     * Update the specified product in storage.
     * 
     * @throws ValidationException
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'min_stock_threshold' => 'sometimes|required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'is_promotional' => 'nullable|boolean',
            'department_id' => 'sometimes|required|exists:departments,id',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product->update($validated);

       
        $this->checkStockThreshold($product);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }


    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }


    public function departmentProducts(Department $department): JsonResponse
    {
        $products = $department->products;

        return response()->json([
            'data' => $products
        ]);
    }


    public function promotionalProducts(Request $request): JsonResponse
    {
        $query = Product::where('is_promotional', true);

        
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $products = $query->get();

        return response()->json([
            'data' => $products
        ]);
    }

    /**
     * Check if product stock is below threshold.
     */
    private function checkStockThreshold(Product $product): void
    {
        if ($product->stock_quantity <= $product->min_stock_threshold) {

            \Log::warning("Product ID {$product->id} ({$product->name}) has low stock: {$product->stock_quantity}");
        }
    }
}