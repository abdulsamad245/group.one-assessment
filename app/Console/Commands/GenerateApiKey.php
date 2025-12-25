<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Models\Brand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * Generate API Key Command
 *
 * Creates a new API key for a specified brand (tenant).
 *
 * Usage:
 *   php artisan apikey:generate {brand_id} {name} [--expires-in=]
 */
class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apikey:generate 
                            {brand : The ID or slug of the brand}
                            {name : A descriptive name for the API key}
                            {--expires-in= : Number of days until expiration (optional)}
                            {--permissions= : Comma-separated list of permissions (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new API key for a brand (tenant)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $brandIdentifier = $this->argument('brand');
        $name = $this->argument('name');
        $expiresIn = $this->option('expires-in');
        $permissions = $this->option('permissions');

        // Find brand by ID or slug
        $brand = is_numeric($brandIdentifier)
            ? Brand::find($brandIdentifier)
            : Brand::where('slug', $brandIdentifier)->first();

        if (!$brand) {
            $this->error("Brand not found with identifier: {$brandIdentifier}");
            return self::FAILURE;
        }

        if (!$brand->is_active) {
            $this->error("Brand '{$brand->name}' is not active.");
            return self::FAILURE;
        }

        // Validate expiration days
        if ($expiresIn !== null) {
            $validator = Validator::make(['expires_in' => $expiresIn], [
                'expires_in' => 'integer|min:1|max:3650',
            ]);

            if ($validator->fails()) {
                $this->error('Expiration days must be between 1 and 3650 (10 years).');
                return self::FAILURE;
            }
        }

        // Parse permissions
        $permissionsArray = null;
        if ($permissions) {
            $permissionsArray = array_map('trim', explode(',', $permissions));
        }

        // Generate API key
        $plainKey = ApiKey::generate();
        $hashedKey = ApiKey::hash($plainKey);
        $prefix = ApiKey::extractPrefix($plainKey);

        // Calculate expiration date
        $expiresAt = $expiresIn ? now()->addDays((int) $expiresIn) : null;

        // Create API key record
        $apiKey = ApiKey::create([
            'brand_id' => $brand->id,
            'name' => $name,
            'key' => $hashedKey,
            'prefix' => $prefix,
            'permissions' => $permissionsArray,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        // Display success message with the plain key
        $this->newLine();
        $this->info('✓ API Key generated successfully!');
        $this->newLine();

        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $apiKey->id],
                ['Brand', $brand->name . ' (ID: ' . $brand->id . ')'],
                ['Name', $name],
                ['Prefix', $prefix],
                ['Expires At', $expiresAt ? $expiresAt->format('Y-m-d H:i:s') : 'Never'],
                ['Permissions', $permissionsArray ? implode(', ', $permissionsArray) : 'None'],
            ]
        );

        $this->newLine();
        $this->warn('⚠ IMPORTANT: Save this API key securely. It will not be shown again!');
        $this->newLine();
        $this->line('API Key: <fg=green>' . $plainKey . '</>');
        $this->newLine();

        $this->comment('Use this key in your API requests:');
        $this->line('  Authorization: Bearer ' . $plainKey);
        $this->line('  or');
        $this->line('  X-API-Key: ' . $plainKey);
        $this->newLine();

        return self::SUCCESS;
    }
}
