<?php

namespace App\Console\Commands;

use App\Models\Report;
use Illuminate\Console\Command;

class CleanupDraftReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:cleanup-drafts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all draft reports from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Report::where('status', 'draft')->count();

        if ($count === 0) {
            $this->info('No draft reports found.');
            return 0;
        }

        $this->info("Found {$count} draft report(s).");

        if ($this->confirm('Do you want to delete all draft reports?', true)) {
            Report::where('status', 'draft')->delete();
            $this->info('✓ Successfully deleted all draft reports.');
        } else {
            $this->info('Operation cancelled.');
        }

        return 0;
    }
}
