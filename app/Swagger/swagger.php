<?php

/**
 * @OA\Info(
 *      title="Supermarket API",
 *      version="1.0.0",
 *      description="API for managing supermarket departments and products",
 *      @OA\Contact(
 *          email="your-email@example.com"
 *      )
 * )
 */

/**
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Supermarket API Server"
 * )
 */

/**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer"
 * )
 */