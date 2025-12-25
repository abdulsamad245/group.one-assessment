<?php

namespace App\Docs;

/**
 * Brand API Documentation
 *
 * This class contains OpenAPI documentation for Brand endpoints.
 */

// class BrandDocs
// {
//     /**
//      * @OA\Get(
//      *     path="/v1/brands",
//      *     summary="List all brands",
//      *     description="Retrieve a list of all brands (tenants) in the system. Returns brands scoped to the authenticated API key.",
//      *     operationId="getBrands",
//      *     tags={"Brands"},
//      *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
//      *     @OA\Response(
//      *         response=200,
//      *         description="Successful operation",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="success", type="boolean", example=true),
//      *             @OA\Property(property="message", type="string", example="Brands found successfully"),
//      *             @OA\Property(
//      *                 property="data",
//      *                 type="array",
//      *                 @OA\Items(ref="#/components/schemas/Brand")
//      *             )
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=401,
//      *         description="Unauthorized - Invalid or missing API key",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="success", type="boolean", example=false),
//      *             @OA\Property(property="message", type="string", example="API key is required")
//      *         )
//      *     )
//      * )
//      */
//     public function index() {}

//     /**
//      * @OA\Post(
//      *     path="/v1/brands",
//      *     summary="Create a new brand",
//      *     description="Create a new brand (tenant) in the system",
//      *     operationId="createBrand",
//      *     tags={"Brands"},
//      *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
//      *     @OA\RequestBody(
//      *         required=true,
//      *         description="Brand data",
//      *         @OA\JsonContent(
//      *             required={"name", "slug"},
//      *             @OA\Property(property="name", type="string", example="Acme Corporation", description="Brand name"),
//      *             @OA\Property(property="slug", type="string", example="acme-corp", description="URL-friendly identifier"),
//      *             @OA\Property(property="description", type="string", example="Leading software company", description="Brand description"),
//      *             @OA\Property(property="contact_email", type="string", format="email", example="contact@acme.com"),
//      *             @OA\Property(property="website", type="string", format="url", example="https://acme.com"),
//      *             @OA\Property(property="settings", type="object", example={"theme": "dark", "notifications": true}),
//      *             @OA\Property(property="is_active", type="boolean", example=true)
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=201,
//      *         description="Brand created successfully",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="success", type="boolean", example=true),
//      *             @OA\Property(property="message", type="string", example="Brand created successfully"),
//      *             @OA\Property(property="data", ref="#/components/schemas/Brand")
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=422,
//      *         description="Validation error",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="success", type="boolean", example=false),
//      *             @OA\Property(property="message", type="string", example="Validation failed")
//      *         )
//      *     )
//      * )
//      */
//     public function store() {}

//     /**
//      * @OA\Get(
//      *     path="/v1/brands/{id}",
//      *     summary="Get a specific brand",
//      *     description="Retrieve details of a specific brand by ID",
//      *     operationId="getBrand",
//      *     tags={"Brands"},
//      *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
//      *     @OA\Parameter(
//      *         name="id",
//      *         in="path",
//      *         description="Brand ID (UUID)",
//      *         required=true,
//      *         @OA\Schema(type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
//      *     ),
//      *     @OA\Response(
//      *         response=200,
//      *         description="Successful operation",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="success", type="boolean", example=true),
//      *             @OA\Property(property="message", type="string", example="Brand found successfully"),
//      *             @OA\Property(property="data", ref="#/components/schemas/Brand")
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=404,
//      *         description="Brand not found",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="success", type="boolean", example=false),
//      *             @OA\Property(property="message", type="string", example="Brand not found")
//      *         )
//      *     )
//      * )
//      */
//     public function show() {}

//     /**
//      * @OA\Put(
//      *     path="/v1/brands/{id}",
//      *     summary="Update a brand",
//      *     description="Update an existing brand's information",
//      *     operationId="updateBrand",
//      *     tags={"Brands"},
//      *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
//      *     @OA\Parameter(
//      *         name="id",
//      *         in="path",
//      *         description="Brand ID (UUID)",
//      *         required=true,
//      *         @OA\Schema(type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
//      *     ),
//      *     @OA\RequestBody(
//      *         required=true,
//      *         @OA\JsonContent(ref="#/components/schemas/Brand")
//      *     ),
//      *     @OA\Response(
//      *         response=200,
//      *         description="Brand updated successfully"
//      *     ),
//      *     @OA\Response(response=404, description="Brand not found")
//      * )
//      */
//     public function update() {}

//     /**
//      * @OA\Delete(
//      *     path="/v1/brands/{id}",
//      *     summary="Delete a brand",
//      *     description="Soft delete a brand. The brand and its associated data will be marked as deleted.",
//      *     operationId="deleteBrand",
//      *     tags={"Brands"},
//      *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
//      *     @OA\Parameter(
//      *         name="id",
//      *         in="path",
//      *         description="Brand ID (UUID)",
//      *         required=true,
//      *         @OA\Schema(type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
//      *     ),
//      *     @OA\Response(
//      *         response=200,
//      *         description="Brand deleted successfully",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="success", type="boolean", example=true),
//      *             @OA\Property(property="message", type="string", example="Brand deleted successfully")
//      *         )
//      *     ),
//      *     @OA\Response(response=404, description="Brand not found")
//      * )
//      */
//     public function destroy() {}
// }
