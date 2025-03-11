<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class DepartmentController extends Controller
{
    /**
 * @OA\Get(
 *     path="/v1/departments",
 *     summary="Get all departments",
 *     tags={"Departments"},
 *     @OA\Response(
 *         response=200,
 *         description="List of departments",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Electronics"),
 *                     @OA\Property(property="description", type="string", example="Electronic devices"),
 *                     @OA\Property(property="slug", type="string", example="electronics"),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
    public function index(): JsonResponse
    {
        $departments = Department::all();
        
        return response()->json([
            'data' => $departments
        ]);
    }

    /**
 * @OA\Post(
 *     path="/v1/departments",
 *     summary="Create a new department",
 *     tags={"Departments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Groceries"),
 *             @OA\Property(property="description", type="string", example="Food and essential items")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Department created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Department created successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Groceries"),
 *                 @OA\Property(property="description", type="string", example="Food and essential items"),
 *                 @OA\Property(property="slug", type="string", example="groceries"),
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
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $department = Department::create($validated);

        return response()->json([
            'message' => 'Department created successfully',
            'data' => $department
        ], 201);
    }

   /**
 * @OA\Get(
 *     path="/v1/departments/{department}",
 *     summary="Get department details",
 *     tags={"Departments"},
 *     @OA\Parameter(
 *         name="department",
 *         in="path",
 *         required=true,
 *         description="Department ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Department details",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Electronics"),
 *                 @OA\Property(property="description", type="string", example="Electronic devices"),
 *                 @OA\Property(property="slug", type="string", example="electronics"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Department not found"
 *     )
 * )
 */
    public function show(Department $department): JsonResponse
    {
        return response()->json([
            'data' => $department
        ]);
    }

   /**
 * @OA\Put(
 *     path="/v1/departments/{department}",
 *     summary="Update a department",
 *     tags={"Departments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="department",
 *         in="path",
 *         required=true,
 *         description="Department ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Electronics and Gadgets"),
 *             @OA\Property(property="description", type="string", example="Electronic devices and accessories")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Department updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Department updated successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Electronics and Gadgets"),
 *                 @OA\Property(property="description", type="string", example="Electronic devices and accessories"),
 *                 @OA\Property(property="slug", type="string", example="electronics-and-gadgets"),
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
 *         response=404,
 *         description="Department not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors"
 *     )
 * )
 */
    public function update(Request $request, Department $department): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $department->update($validated);

        return response()->json([
            'message' => 'Department updated successfully',
            'data' => $department
        ]);
    }

    /**
 * @OA\Delete(
 *     path="/v1/departments/{department}",
 *     summary="Delete a department",
 *     tags={"Departments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="department",
 *         in="path",
 *         required=true,
 *         description="Department ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Department deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Department deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Department not found"
 *     )
 * )
 */
    public function destroy(Department $department): JsonResponse
    {
        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully'
        ]);
    }
}