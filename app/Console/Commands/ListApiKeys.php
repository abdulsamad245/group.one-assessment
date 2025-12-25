<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Models\Brand;
use Illuminate\Console\Command;

/**
 * List API Keys Command
 *
 * Lists all API keys for a specified brand or all brands.
 */
class ListApiKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apikey:list 
                            {brand? : The ID or slug of the brand (optional)}
                            {--all : Show all API keys including inactive and expired}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List API keys for a brand or all brands';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $brandIdentifier = $this->argument('brand');
        $showAll = $this->option('all');

        $query = ApiKey::with('brand');

        if ($brandIdentifier) {
            $brand = is_numeric($brandIdentifier)
                ? Brand::find($brandIdentifier)
                : Brand::where('slug', $brandIdentifier)->first();

            if (!$brand) {
                $this->error("Brand not found with identifier: {$brandIdentifier}");
                return self::FAILURE;
            }

            $query->where('brand_id', $brand->id);
            $this->info("API Keys for Brand: {$brand->name}");
        } else {
            $this->info('All API Keys');
        }

        if (!$showAll) {
            $query->active();
        }

        $apiKeys = $query->orderBy('created_at', 'desc')->get();

        if ($apiKeys->isEmpty()) {
            $this->warn('No API keys found.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->table(
            ['ID', 'Brand', 'Name', 'Prefix', 'Status', 'Last Used', 'Expires At', 'Created At'],
            $apiKeys->map(function ($key) {
                $status = $key->isValid() ? '<fg=green>Active</>' : '<fg=red>Inactive</>';

                return [
                    $key->id,
                    $key->brand->name,
                    $key->name,
                    $key->prefix,
                    $status,
                    $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Never',
                    $key->expires_at ? $key->expires_at->format('Y-m-d') : 'Never',
                    $key->created_at->format('Y-m-d H:i'),
                ];
            })->toArray()
        );

        return self::SUCCESS;
    }
}
