<?php

namespace App\OpenApi;

/**
 * @OA\Schema(
 *     schema="Product",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Smartphone"),
 *     @OA\Property(property="description", type="string", example="Latest model smartphone"),
 *     @OA\Property(property="price", type="number", format="float", example=699.99),
 *     @OA\Property(property="stock_quantity", type="integer", example=50),
 *     @OA\Property(property="min_stock_threshold", type="integer", example=10),
 *     @OA\Property(property="slug", type="string", example="smartphone"),
 *     @OA\Property(property="category", type="string", example="Phones"),
 *     @OA\Property(property="is_promotional", type="boolean", example=true),
 *     @OA\Property(property="department_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

/**
 * @OA\Schema(
 *     schema="ProductRequest",
 *     required={"name", "price", "stock_quantity", "min_stock_threshold", "department_id"},
 *     @OA\Property(property="name", type="string", example="Smartphone"),
 *     @OA\Property(property="description", type="string", example="Latest model smartphone"),
 *     @OA\Property(property="price", type="number", format="float", example=699.99),
 *     @OA\Property(property="stock_quantity", type="integer", example=50),
 *     @OA\Property(property="min_stock_threshold", type="integer", example=10),
 *     @OA\Property(property="category", type="string", example="Phones"),
 *     @OA\Property(property="is_promotional", type="boolean", example=true),
 *     @OA\Property(property="department_id", type="integer", example=1)
 * )
 */

/**
 * @OA\Schema(
 *     schema="ProductUpdateRequest",
 *     @OA\Property(property="name", type="string", example="Smartphone Pro"),
 *     @OA\Property(property="description", type="string", example="Latest model smartphone with enhanced features"),
 *     @OA\Property(property="price", type="number", format="float", example=899.99),
 *     @OA\Property(property="stock_quantity", type="integer", example=40),
 *     @OA\Property(property="min_stock_threshold", type="integer", example=8),
 *     @OA\Property(property="category", type="string", example="Premium Phones"),
 *     @OA\Property(property="is_promotional", type="boolean", example=true),
 *     @OA\Property(property="department_id", type="integer", example=1)
 * )
 */

/**
 * @OA\Schema(
 *     schema="Department",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Electronics"),
 *     @OA\Property(property="description", type="string", example="Electronic devices and accessories"),
 *     @OA\Property(property="slug", type="string", example="electronics"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class Schemas
{
    // This is just a placeholder class for the annotations
}