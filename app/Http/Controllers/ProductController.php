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
    
/**
 * @OA\Get(
 *     path="/v1/products",
 *     summary="Get all products with optional filtering",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="department_id",
 *         in="query",
 *         required=false,
 *         description="Filter by department ID",
 *         
 *     ),
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         required=false,
 *         description="Filter by category",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="promotional",
 *         in="query",
 *         required=false,
 *         description="Filter promotional products",
 *         @OA\Schema(type="boolean")
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         required=false,
 *         description="Search by product name",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of products",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Smartphone"),
 *                     @OA\Property(property="description", type="string", example="Latest model smartphone"),
 *                     @OA\Property(property="price", type="number", format="float", example=699.99),
 *                     @OA\Property(property="stock_quantity", type="integer", example=50),
 *                     @OA\Property(property="min_stock_threshold", type="integer", example=10),
 *                     @OA\Property(property="slug", type="string", example="smartphone"),
 *                     @OA\Property(property="category", type="string", example="Phones"),
 *                     @OA\Property(property="is_promotional", type="boolean", example=true),
 *                     @OA\Property(property="department_id", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     )
 * )
 */
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
 * @OA\Post(
 *     path="/v1/products",
 *     summary="Create a new product",
 *     tags={"Products"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "price", "stock_quantity", "min_stock_threshold", "department_id"},
 *             @OA\Property(property="name", type="string", example="Smartphone"),
 *             @OA\Property(property="description", type="string", example="Latest model smartphone"),
 *             @OA\Property(property="price", type="number", format="float", example=699.99),
 *             @OA\Property(property="stock_quantity", type="integer", example=50),
 *             @OA\Property(property="min_stock_threshold", type="integer", example=10),
 *             @OA\Property(property="category", type="string", example="Phones"),
 *             @OA\Property(property="is_promotional", type="boolean", example=true),
 *             @OA\Property(property="department_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Product created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Product created successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Smartphone"),
 *                 @OA\Property(property="description", type="string", example="Latest model smartphone"),
 *                 @OA\Property(property="price", type="number", format="float", example=699.99),
 *                 @OA\Property(property="stock_quantity", type="integer", example=50),
 *                 @OA\Property(property="min_stock_threshold", type="integer", example=10),
 *                 @OA\Property(property="slug", type="string", example="smartphone"),
 *                 @OA\Property(property="category", type="string", example="Phones"),
 *                 @OA\Property(property="is_promotional", type="boolean", example=true),
 *                 @OA\Property(property="department_id", type="integer", example=1),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors"
 *     )
 * )
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

/**
 * @OA\Get(
 *     path="/v1/products/{product}",
 *     summary="Get product details",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="product",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'data' => $product
        ]);
    }

    /**
 * @OA\Put(
 *     path="/v1/products/{product}",
 *     summary="Update a product",
 *     tags={"Products"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="product",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors"
 *     )
 * )
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

/**
 * @OA\Delete(
 *     path="/v1/products/{product}",
 *     summary="Delete a product",
 *     tags={"Products"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="product",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Product deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

/**
 * @OA\Get(
 *     path="/v1/departments/{department}/products",
 *     summary="Get products for a specific department",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="department",
 *         in="path",
 *         required=true,
 *         description="Department ID",
 *         
 *     ),
 *   
 *     @OA\Response(
 *         response=404,
 *         description="Department not found"
 *     )
 * )
 */
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

   
    private function checkStockThreshold(Product $product): void
    {
        if ($product->stock_quantity <= $product->min_stock_threshold) {

            \Log::warning("Product ID {$product->id} ({$product->name}) has low stock: {$product->stock_quantity}");
        }
    }
}