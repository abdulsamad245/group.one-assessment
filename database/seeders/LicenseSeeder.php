<?php

namespace Database\Seeders;

use App\Models\Activation;
use App\Models\Brand;
use App\Models\License;
use App\Models\LicenseKey;
use Illuminate\Database\Seeder;

class LicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wpRocket = Brand::where('slug', 'wp-rocket')->first();
        $imagify = Brand::where('slug', 'imagify')->first();
        $rankmath = Brand::where('slug', 'rankmath')->first();

        // WP Rocket Licenses
        $this->createLicenseWithKeys($wpRocket, [
            'customer_email' => 'john@mailinator.com',
            'customer_name' => 'John Doe',
            'product_name' => 'WP Rocket Pro',
            'product_slug' => 'wp-rocket-pro',
            'product_sku' => 'WPR-PRO-001',
            'license_type' => 'subscription',
            'max_activations_per_instance' => ['site_url' => 3, 'host' => 5],
            'expires_at' => now()->addYear(),
            'status' => 'active',
        ], 2, 1);

        $this->createLicenseWithKeys($wpRocket, [
            'customer_email' => 'jane@mailinator.com',
            'customer_name' => 'Jane Smith',
            'product_name' => 'WP Rocket Business',
            'product_slug' => 'wp-rocket-business',
            'product_sku' => 'WPR-BUS-001',
            'license_type' => 'subscription',
            'max_activations_per_instance' => ['site_url' => 10, 'host' => 20],
            'expires_at' => now()->addMonths(6),
            'status' => 'active',
        ], 1, 0);

        // Imagify Licenses
        $this->createLicenseWithKeys($imagify, [
            'customer_email' => 'john@mailinator.com',
            'customer_name' => 'John Doe',
            'product_name' => 'Imagify Unlimited',
            'product_slug' => 'imagify-unlimited',
            'product_sku' => 'IMG-UNL-001',
            'license_type' => 'subscription',
            'max_activations_per_instance' => ['site_url' => 5],
            'expires_at' => now()->addYear(),
            'status' => 'active',
        ], 1, 1);

        $this->createLicenseWithKeys($imagify, [
            'customer_email' => 'bob@mailinator.com',
            'customer_name' => 'Bob Johnson',
            'product_name' => 'Imagify Pro',
            'product_slug' => 'imagify-pro',
            'product_sku' => 'IMG-PRO-001',
            'license_type' => 'perpetual',
            'max_activations_per_instance' => ['site_url' => 1, 'machine_id' => 1],
            'expires_at' => null,
            'status' => 'active',
        ], 1, 1);

        // RankMath Licenses
        $this->createLicenseWithKeys($rankmath, [
            'customer_email' => 'alice@mailinator.com',
            'customer_name' => 'Alice Williams',
            'product_name' => 'RankMath Pro',
            'product_slug' => 'rankmath-pro',
            'product_sku' => 'RM-PRO-001',
            'license_type' => 'subscription',
            'max_activations_per_instance' => ['site_url' => 100],
            'expires_at' => now()->addYear(),
            'status' => 'active',
        ], 2, 2);

        // Suspended license
        $this->createLicenseWithKeys($wpRocket, [
            'customer_email' => 'suspended@mailinator.com',
            'customer_name' => 'Suspended User',
            'product_name' => 'WP Rocket Pro',
            'product_slug' => 'wp-rocket-pro',
            'product_sku' => 'WPR-PRO-002',
            'license_type' => 'subscription',
            'max_activations_per_instance' => ['site_url' => 3],
            'expires_at' => now()->addYear(),
            'status' => 'suspended',
        ], 1, 0);

        // Expired license
        $this->createLicenseWithKeys($imagify, [
            'customer_email' => 'expired@mailinator.com',
            'customer_name' => 'Expired User',
            'product_name' => 'Imagify Pro',
            'product_slug' => 'imagify-pro',
            'product_sku' => 'IMG-PRO-002',
            'license_type' => 'subscription',
            'max_activations_per_instance' => ['site_url' => 1],
            'expires_at' => now()->subDays(30),
            'status' => 'expired',
        ], 1, 0);
    }

    /**
     * Create a license with license keys and activations.
     */
    private function createLicenseWithKeys(
        Brand $brand,
        array $licenseData,
        int $keyCount = 1,
        int $activationsPerKey = 0
    ): void {
        $totalActivations = 0;

        for ($i = 0; $i < $keyCount; $i++) {
            // First, create the LicenseKey
            $licenseKey = LicenseKey::factory()->create([
                'brand_id' => $brand->id,
                'customer_email' => $licenseData['customer_email'],
                'status' => $licenseData['status'] === 'active' ? 'active' : 'inactive',
                'expires_at' => $licenseData['expires_at'],
            ]);

            // Then create the License referencing the LicenseKey
            $license = License::create(array_merge($licenseData, [
                'license_key_id' => $licenseKey->id,
                'brand_id' => $brand->id,
                'current_activations' => 0,
            ]));

            // Create activations
            $instanceTypes = array_keys($licenseData['max_activations_per_instance']);
            for ($j = 0; $j < $activationsPerKey; $j++) {
                $instanceType = $instanceTypes[$j % count($instanceTypes)] ?? 'site_url';
                Activation::factory()->create([
                    'license_key_id' => $licenseKey->id,
                    'license_id' => $license->id,
                    'instance_type' => $instanceType,
                    'instance_value' => match ($instanceType) {
                        'site_url' => 'https://site' . ($j + 1) . '-key' . ($i + 1) . '.mailinator.com',
                        'host' => 'server' . ($j + 1) . '-key' . ($i + 1) . '.local',
                        'machine_id' => 'MACHINE-' . strtoupper(substr(md5($j . $licenseKey->id), 0, 8)),
                        default => 'instance-' . ($j + 1),
                    },
                    'status' => 'active',
                ]);
                $totalActivations++;
            }

            // Update current_activations count
            $license->update([
                'current_activations' => $activationsPerKey,
            ]);
        }
    }
}

