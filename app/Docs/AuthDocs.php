<?php

namespace App\Docs;

/**
 * Authentication API Documentation
 */
class AuthDocs
{
    /**
     * @OA\Post(
     *     path="/v1/auth/register",
     *     summary="Register a new user and brand",
     *     description="Register a new user account along with a new brand. Returns user details, brand info, and an initial API key.",
     *     operationId="register",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation", "brand_name"},
     *             @OA\Property(property="name", type="string", example="John Doe", description="User's full name"),
     *             @OA\Property(property="email", type="string", format="email", example="john@mailinator.com", description="User's email address"),
     *             @OA\Property(property="password", type="string", format="password", example="password123", description="Password (min 8 characters)"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123", description="Password confirmation"),
     *             @OA\Property(property="brand_name", type="string", example="Acme Corporation", description="Brand/company name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User"),
     *                 @OA\Property(property="brand", ref="#/components/schemas/Brand"),
     *                 @OA\Property(property="api_key", type="string", example="lcs_xxxxxxxx.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", description="Initial API key for the brand")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function register() {}

    /**
     * @OA\Post(
     *     path="/v1/auth/login",
     *     summary="Login user",
     *     description="Authenticate a user and receive a Sanctum Bearer token for account management operations.",
     *     operationId="login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@mailinator.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged in successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User"),
     *                 @OA\Property(property="token", type="string", example="1|abc123def456...", description="Sanctum Bearer token")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function login() {}

    /**
     * @OA\Post(
     *     path="/v1/auth/logout",
     *     summary="Logout user",
     *     description="Invalidate the current user's authentication token.",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     security={{"SanctumAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function logout() {}

    // /**
    //  * @OA\Get(
    //  *     path="/v1/auth/me",
    //  *     summary="Get current user",
    //  *     description="Retrieve the authenticated user's profile information including their brand.",
    //  *     operationId="me",
    //  *     tags={"Authentication"},
    //  *     security={{"SanctumAuth": {}}},
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="User retrieved successfully",
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="success", type="boolean", example=true),
    //  *             @OA\Property(property="message", type="string", example="User retrieved successfully"),
    //  *             @OA\Property(
    //  *                 property="data",
    //  *                 type="object",
    //  *                 @OA\Property(property="user", ref="#/components/schemas/User")
    //  *             )
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=401,
    //  *         description="Unauthenticated"
    //  *     )
    //  * )
    //  */
    // public function me() {}
}

