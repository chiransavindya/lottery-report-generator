<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UploadFile;

class CleanupProcessedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploads:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete physical XML files for processed uploads to save space';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of processed files...');

        $files = UploadFile::where('status', 'completed')->get();
        $count = 0;
        $sizeFreed = 0;

        foreach ($files as $file) {
            if ($file->file_path && file_exists($file->file_path)) {
                try {
                    $size = filesize($file->file_path);
                    unlink($file->file_path);
                    $count++;
                    $sizeFreed += $size;
                    $this->line("Deleted: {$file->file_path}");
                } catch (\Exception $e) {
                    $this->error("Failed to delete {$file->file_path}: " . $e->getMessage());
                }
            }
        }

        $mb = round($sizeFreed / 1024 / 1024, 2);
        $this->info("Cleanup complete. Deleted {$count} files. Freed {$mb} MB.");
    }
}
