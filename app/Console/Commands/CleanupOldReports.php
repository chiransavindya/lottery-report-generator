<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupOldReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:cleanup-old {--days=7 : Number of days to keep reports}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete PDF reports older than X days to save disk space';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Starting cleanup of PDF reports older than {$days} days...");

        $cutoffDate = Carbon::now()->subDays($days);
        $count = 0;
        $sizeFreed = 0;

        // Get all files recursively from reports directory
        $files = Storage::allFiles('reports');

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'pdf') {
                continue;
            }

            try {
                $lastModified = Storage::lastModified($file);
                $fileDate = Carbon::createFromTimestamp($lastModified);

                if ($fileDate->lt($cutoffDate)) {
                    $size = Storage::size($file);
                    Storage::delete($file);
                    $count++;
                    $sizeFreed += $size;
                    $this->line("Deleted: {$file} (Age: {$fileDate->diffForHumans()})");
                }
            } catch (\Exception $e) {
                $this->error("Failed to process {$file}: " . $e->getMessage());
            }
        }

        $mb = round($sizeFreed / 1024 / 1024, 2);
        $this->info("Cleanup complete. Deleted {$count} PDF files. Freed {$mb} MB.");

        // Clean up empty directories
        $this->cleanupEmptyDirectories();
    }

    /**
     * Remove empty directories from reports storage.
     */
    protected function cleanupEmptyDirectories(): void
    {
        $directories = Storage::directories('reports');

        foreach ($directories as $dir) {
            $this->cleanupDirectoryRecursive($dir);
        }
    }

    /**
     * Recursively clean up empty directories.
     */
    protected function cleanupDirectoryRecursive(string $directory): void
    {
        $subdirs = Storage::directories($directory);

        foreach ($subdirs as $subdir) {
            $this->cleanupDirectoryRecursive($subdir);
        }

        // Check if directory is empty after cleaning subdirectories
        $files = Storage::files($directory);
        $dirs = Storage::directories($directory);

        if (empty($files) && empty($dirs)) {
            Storage::deleteDirectory($directory);
            $this->line("Removed empty directory: {$directory}");
        }
    }
}
