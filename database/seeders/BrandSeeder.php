<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'WP Rocket',
                'slug' => 'wp-rocket',
                'description' => 'The best WordPress performance plugin',
                'contact_email' => 'support@wp-rocket.me',
                'website' => 'https://wp-rocket.me',
                'settings' => [
                    'theme' => 'light',
                    'timezone' => 'UTC',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Imagify',
                'slug' => 'imagify',
                'description' => 'Image optimization made easy',
                'contact_email' => 'support@imagify.io',
                'website' => 'https://imagify.io',
                'settings' => [
                    'theme' => 'light',
                    'timezone' => 'UTC',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'RankMath',
                'slug' => 'rankmath',
                'description' => 'SEO plugin for WordPress',
                'contact_email' => 'support@rankmath.com',
                'website' => 'https://rankmath.com',
                'settings' => [
                    'theme' => 'dark',
                    'timezone' => 'UTC',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brandData) {
            Brand::create($brandData);
        }
    }
}
