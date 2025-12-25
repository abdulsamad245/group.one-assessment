<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

/**
 * Revoke API Key Command
 *
 * Revokes (deactivates) an API key.
 */
class RevokeApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apikey:revoke 
                            {id : The ID of the API key to revoke}
                            {--delete : Permanently delete the API key instead of deactivating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke (deactivate) or delete an API key';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $id = $this->argument('id');
        $delete = $this->option('delete');

        $apiKey = ApiKey::with('brand')->find($id);

        if (!$apiKey) {
            $this->error("API key not found with ID: {$id}");
            return self::FAILURE;
        }

        $this->info("API Key Details:");
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $apiKey->id],
                ['Brand', $apiKey->brand->name],
                ['Name', $apiKey->name],
                ['Prefix', $apiKey->prefix],
                ['Status', $apiKey->isValid() ? 'Active' : 'Inactive'],
            ]
        );

        $action = $delete ? 'delete' : 'revoke';

        if (!$this->confirm("Are you sure you want to {$action} this API key?")) {
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        if ($delete) {
            $apiKey->delete();
            $this->info('✓ API key deleted successfully.');
        } else {
            $apiKey->is_active = false;
            $apiKey->save();
            $this->info('✓ API key revoked successfully.');
        }

        return self::SUCCESS;
    }
}
