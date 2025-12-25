<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wpRocket = Brand::where('slug', 'wp-rocket')->first();
        $imagify = Brand::where('slug', 'imagify')->first();
        $rankmath = Brand::where('slug', 'rankmath')->first();

        // Super Admin (can access all brands)
        User::create([
            'brand_id' => $wpRocket->id,
            'name' => 'Super Admin',
            'email' => 'superadmin@group.one',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // WP Rocket Admin
        User::create([
            'brand_id' => $wpRocket->id,
            'name' => 'WP Rocket Admin',
            'email' => 'admin@wp-rocket.me',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // WP Rocket User
        User::create([
            'brand_id' => $wpRocket->id,
            'name' => 'WP Rocket User',
            'email' => 'user@wp-rocket.me',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Imagify Admin
        User::create([
            'brand_id' => $imagify->id,
            'name' => 'Imagify Admin',
            'email' => 'admin@imagify.io',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // RankMath Admin
        User::create([
            'brand_id' => $rankmath->id,
            'name' => 'RankMath Admin',
            'email' => 'admin@rankmath.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create API keys for each brand with deterministic test keys
        $this->createApiKey($wpRocket, 'WP Rocket Default API Key', 'lcs_wprocket.test_api_key_for_wp_rocket_brand');
        $this->createApiKey($imagify, 'Imagify Default API Key', 'lcs_imagify0.test_api_key_for_imagify_brand0');
        $this->createApiKey($rankmath, 'RankMath Default API Key', 'lcs_rankmath.test_api_key_for_rankmath_brand');
    }

    /**
     * Create an API key for a brand with a deterministic key for testing.
     */
    private function createApiKey(Brand $brand, string $name, string $testKey): void
    {
        // Use deterministic prefix for testing
        $prefix = substr($testKey, 0, 12); // "lcs_" + 8 chars

        ApiKey::create([
            'id' => Str::uuid()->toString(),
            'brand_id' => $brand->id,
            'name' => $name,
            'key' => hash('sha256', $testKey),
            'prefix' => $prefix,
            'permissions' => ['*'],
            'is_active' => true,
        ]);

        // Output the plain key for reference (only visible during seeding)
        $this->command->info("  API Key for {$brand->name}: {$testKey}");
    }
}
