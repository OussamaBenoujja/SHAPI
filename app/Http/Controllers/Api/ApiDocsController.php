<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Supermarket API Documentation",
 *     description="Documentation for Supermarket Departments API",
 *     @OA\Contact(
 *         email="contact@example.com",
 *         name="API Support"
 *     )
 * )
 * 
 * @OA\Server(
 *     description="Local API Server",
 *     url="http://localhost:8000/api"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 * 
 * @OA\Tag(
 *     name="Departments",
 *     description="API Endpoints for Department management"
 * )
 * 
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints for Product management"
 * )
 * 
 * @OA\Tag(
 *     name="Stock",
 *     description="API Endpoints for Stock management"
 * )
 */

 
class ApiDocsController extends Controller
{
    //
}