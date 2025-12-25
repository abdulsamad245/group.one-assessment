<?php

namespace App\Services;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\BrandRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly BrandRepository $brandRepository,
        private readonly ApiKeyService $apiKeyService
    ) {
    }

    /**
     * Register a new user with brand and API key.
     */
    public function register(RegisterDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            $brand = $this->brandRepository->create([
                'id' => Str::uuid()->toString(),
                'name' => $dto->getBrandName(),
                'slug' => $dto->getBrandSlug(),
                'is_active' => true,
            ]);

            $dto->setBrandId($brand->id);

            $user = $this->userRepository->create([
                'id' => Str::uuid()->toString(),
                'brand_id' => $brand->id,
                'name' => $dto->getName(),
                'email' => $dto->getEmail(),
                'password' => Hash::make($dto->getPassword()),
                'role' => $dto->getRole(),
            ]);

            $this->apiKeyService->generateApiKey(
                $brand->id,
                'Default API Key'
            );

            return [
                'user' => $user->load('brand'),
                'brand' => $brand
            ];
        });
    }

    /**
     * Login a user and return token.
     */
    public function login(LoginDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->getEmail());

        if (!$user || !Hash::check($dto->getPassword(), $user->password)) {
            throw new \Exception(__('messages.invalid-credentials'));
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load('brand'),
            'token' => $token,
        ];
    }

    /**
     * Logout a user.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
