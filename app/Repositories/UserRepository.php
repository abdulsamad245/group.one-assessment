<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * Find user by ID.
     */
    public function findById(string $id): ?User
    {
        return User::with('brand')->find($id);
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::with('brand')->where('email', $email)->first();
    }

    /**
     * Find users by brand.
     */
    public function findByBrand(string $brandId): Collection
    {
        return User::where('brand_id', $brandId)->get();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update a user.
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
