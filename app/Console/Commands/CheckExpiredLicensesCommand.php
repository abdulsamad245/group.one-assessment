<?php

namespace App\Console\Commands;

use App\Jobs\CheckExpiredLicensesJob;
use Illuminate\Console\Command;

class CheckExpiredLicensesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licenses:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and mark expired licenses';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Dispatching job to check expired licenses...');

        CheckExpiredLicensesJob::dispatch();

        $this->info('Job dispatched successfully!');

        return Command::SUCCESS;
    }
}
