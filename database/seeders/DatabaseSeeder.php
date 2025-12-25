<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('Starting Database Seeding...');
        $this->command->info('========================================');
        $this->command->newLine();

        $this->call([
            BrandSeeder::class,
            UserSeeder::class,
            LicenseSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('========================================');
        $this->command->newLine();

        $this->command->info('Test Credentials:');
        $this->command->info('Super Admin: superadmin@group.one / password');
        $this->command->info('WP Rocket Admin: admin@wp-rocket.me / password');
        $this->command->info('Imagify Admin: admin@imagify.io / password');
        $this->command->info('RankMath Admin: admin@rankmath.com / password');
        $this->command->newLine();
    }
}
